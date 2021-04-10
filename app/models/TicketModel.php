<?php
require_once APPROOT . "/classes/Ticket.php";
require_once APPROOT . "/classes/Activity.php";
require_once APPROOT . "/helper/QueryStringHelper.php";

class TicketModel extends Database {

    public function getSelecetdTickets($invoiceId){
        $this->query('
        SELECT
        ST.`Participants`,
        ST.`Price`,
        A.`StartDateTime` as Date,
        A.`Name`
        FROM `SelectedTicket` AS ST
        JOIN `Ticket` T ON ST.`TicketId` = T.`TicketId`
        JOIN `TicketActivityLink` TAL ON T.`TicketId` = TAL.`TicketId`
        JOIN `Activity` A ON TAL.`ActivityId` = A.`ActivityId`
        WHERE ST.InvoiceId = :id ;');

        $this->bind(':id', $invoiceId);
        $result = $this->resultSet();

        return $result;
    }

    public function makeSelectedTicket($data){
        $this->query('INSERT into `SelectedTicket` VALUES(null,:Price, :Participants, :Comment, :InvoiceId, :TicketId)');

        $this->bind(':Price', $data['Price']);
        $this->bind(':Participants', $data['Participants']);
        $this->bind(':Comment', $data['Comment']);
        $this->bind(':InvoiceId', $data['InvoiceId']);
        $this->bind(':TicketId', $data['TicketId']);

        return $this->execute();

    }
    public function makeTicket($name, $price, $eventId){
        $this->query('INSERT INTO `Ticket`(`Name`, `Price`, `EventId`) VALUES (:name, :price, :id)');

        $this->bind(':name', $name);
        $this->bind(':price', $price);
        $this->bind(':id', $eventId);

        return $this->execute();
    }

    public function getTickets(){
        $this->query('SELECT * FROM Ticket');
        $result = $this->resultSet();

        return $result;
    }

    public function getTicketOverview($eventId) {
        $this->query(
            "SELECT
                `Ticket`.`TicketId`,
                `Ticket`.`Name` AS `TicketName`,
                `Price`,
                A.`ActivityId`
            FROM `Ticket`
            JOIN TicketActivityLink TAL on Ticket.TicketId = TAL.TicketId
            JOIN Activity A on TAL.ActivityId = A.ActivityId
            WHERE `Ticket`.`EventId` = :eventId;"
        );

        $this->bind(":eventId", $eventId);

        return $this->mapTicketActivityConcise($this->resultSet());
    }

    public function getTicketConcise($ticketId) {
        $this->query(
            "SELECT
                `Ticket`.`TicketId`,
                `Ticket`.`Name` AS `TicketName`,
                `Price`,
                A.`ActivityId`
            FROM `Ticket`
            JOIN TicketActivityLink TAL on Ticket.TicketId = TAL.TicketId
            JOIN Activity A on TAL.ActivityId = A.ActivityId
            WHERE `Ticket`.`TicketId` = :ticketId;"
        );

        $this->bind(":ticketId", $ticketId);

        return $this->mapTicketActivityConcise($this->resultSet())[0];
    }

    private function mapTicketActivityConcise($rows) {
        $ticketIdMap = [];
        foreach ($rows as $row) {
            $ticketId = $row->TicketId;

            if (!isset($ticketIdMap[$ticketId])) {
                $ticketIdMap[$ticketId] = new Ticket(
                    intval($row->TicketId),
                    $row->TicketName,
                    floatval($row->Price)
                );
            }

            $ticket = $ticketIdMap[$ticketId];
            $ticket->addActivity(
                new Activity(
                    intval($row->ActivityId),
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                )
            );
        }

        return array_values($ticketIdMap);
    }

    public function deleteAllActivityLinks($ticketId) {
        $this->query(
            "DELETE FROM `TicketActivityLink`
            WHERE `TicketId` = :ticketId;"
        );

        $this->bind(":ticketId", $ticketId);
        $this->execute();
    }

    public function updateTicket($ticketId, $name, $price) {
        $this->query(
            "UPDATE `Ticket`
            SET `Name` = :name,
                `Price` = :price
            WHERE `TicketId` = :ticketId;"
        );

        $this->bind(":ticketId", $ticketId);
        $this->bind(":name", $name);
        $this->bind(":price", $price);

        $this->execute();
    }

    public function deleteTicket($ticketId) {
        $this->query(
            "DELETE FROM `Ticket`
            WHERE `TicketId` = :ticketId"
        );

        $this->bind(":ticketId", $ticketId);

        $this->execute();
    }

    public function createTicketActivityLink($ticketId, $activityId) {
        $this->query(
            "INSERT INTO `TicketActivityLink`
            VALUES (:ticketId, :activityId);"
        );

        $this->bind(":ticketId", $ticketId);
        $this->bind(":activityId", $activityId);

        $this->execute();
    }

    public function updateCapacity($id , $ticketsLeft){
        $this->query('UPDATE `Activity` SET `Capacity` = :ticketsLeft WHERE ActivityId = :id');
    }

    public function getTicketById($id){
        $this->query('SELECT * FROM Ticket  WHERE TicketId = :id');

        $this->bind(':id',$id);
        $result = $this->single();

        return $result;
    }

    public function getTicketsCartInfo() {
        $this->query(
            "SELECT Ticket.`TicketId`, Ticket.`Name`, `StartDateTime`, `EndDateTime`, A.`EventId`
            FROM `Ticket`
            JOIN TicketActivityLink TAL on Ticket.TicketId = TAL.TicketId
            JOIN Activity A on TAL.ActivityId = A.ActivityId;"
        );

        $entries = $this->resultSet();
        return $this->mapTicketActivitiesCartInfo($entries);
    }

    private function mapTicketActivitiesCartInfo($ticketActivityCombos) {
        $ticketList = [];
        foreach ($ticketActivityCombos as $entry) {
            $ticketId = $entry->TicketId;
            
            if (!isset($ticketList[$ticketId])) {
                $ticketList[$ticketId] = new Ticket(
                    intval($entry->TicketId),
                    $entry->Name,
                    null
                );
            }

            $ticketList[$ticketId]->addActivity(
                new Activity(
                    null,
                    null,
                    null,
                    null,
                    $entry->StartDateTime,
                    $entry->EndDateTime,
                    $entry->EventId,
                    null,
                    null,
                    null,
                    null
                )
            );
        }

        return array_values($ticketList);
    }
}
