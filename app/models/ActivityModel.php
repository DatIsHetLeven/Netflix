<?php
require_once APPROOT . "/classes/Activity.php";
require_once APPROOT . "/classes/Location.php";
require_once APPROOT . "/classes/Hall.php";
require_once APPROOT . "/classes/Artist.php";

class ActivityModel extends Database {
    public function activityExists($activityId) {
        $this->query(
            "SELECT 'Present'
            FROM `Activity`
            WHERE `ActivityId` = :activityId;"
        );

        $this->bind(":activityId", $activityId);
        $rows = $this->resultSet();

        return count($rows) > 0;
    }

    public function getHistoricTours(){
        $this->query('SELECT
        A.`ActivityId` as id,
        A.`capacity`,
        (SELECT SUM(Participants)
        FROM `SelectedTicket`
        JOIN `Ticket` T on `SelectedTicket`.`TicketId` = T.`TicketId`
        JOIN `TicketActivityLink` TAL on T.`TicketId` = TAL.`TicketId`
        JOIN `Activity` A2 on TAL.`ActivityId` = A2.`ActivityId`
        WHERE A.`ActivityId` = A2.`ActivityId`
        )as booked,
        A.`language`,
        DAYNAME(A.`startDateTime`) as day,
        TIME_FORMAT(A.`startDateTime`, "%H:%i") as time,
        A.`StartDateTime` as datetime
        FROM `Activity` AS A
        WHERE A.EventId = :eventId
        ORDER BY A.`StartDateTime`;');

        $this->bind(':eventId',3);
        $result = $this->resultSet();

        return $result;
    }

    public function getHistoricTourById($id){
        $this->query('SELECT
        A.`ActivityId` as id,
        A.`capacity`,
        (SELECT SUM(Participants)
        FROM `SelectedTicket`
        JOIN `Ticket` T on `SelectedTicket`.`TicketId` = T.`TicketId`
        JOIN `TicketActivityLink` TAL on T.`TicketId` = TAL.`TicketId`
        JOIN `Activity` A2 on TAL.`ActivityId` = A2.`ActivityId`
        WHERE A.`ActivityId` = A2.`ActivityId`
        ) AS booked ,
        A.`language`,
        DAYNAME(A.`startDateTime`) as day,
        TIME_FORMAT(A.`startDateTime`, "%H:%i") as time,
        A.`StartDateTime` as datetime,
        T.`price`
        FROM `Activity` AS A
        JOIN `TicketActivityLink` TAL ON A.`ActivityId` = TAL.`ActivityId`
        JOIN `Ticket` T ON TAL.`TicketId` = T.`TicketId`
        WHERE A.ActivityId = :activityId
        ORDER BY A.`StartDateTime`;');

        $this->bind(':activityId',$id);
        $result = $this->single();

        return $result;
    }

    public function getPerformanceOverviewByEventId($eventId) {
        $this->query(
            "SELECT
                A.`ActivityId`,
                A.`Name` AS `ActivityName`,
                A.`StartDateTime`,
                A.`EndDateTime`,
                `Location`.`LocationId`,
                `Location`.`Name` AS `LocationName`,
                `Location`.`Seats` AS `LocationSeats`,
                `Hall`.`HallId`,
                `Hall`.`Name` AS `HallName`,
                `Hall`.`Seats` AS `HallSeats`,
                `Artist`.`ArtistId`,
                `Artist`.`Name` AS `ArtistName`,
                (
                    SELECT count(*)
                    FROM `SelectedTicket`
                    JOIN `Ticket` T on `SelectedTicket`.`TicketId` = T.`TicketId`
                    JOIN `TicketActivityLink` TAL on T.`TicketId` = TAL.`TicketId`
                    JOIN `Activity` A2 on TAL.`ActivityId` = A2.`ActivityId`
                    WHERE A.`ActivityId` = A2.`ActivityId`
                ) AS SeatsTaken
            FROM `Activity` AS A
            JOIN `Location` ON A.`LocationId` = `Location`.`LocationId`
            LEFT JOIN `Hall` ON A.`HallId` = `Hall`.`HallId`
            JOIN `ActivityArtistLink` ON `ActivityArtistLink`.`ActivityId` = A.`ActivityId`
            JOIN `Artist` ON `ActivityArtistLink`.`ArtistId` = `Artist`.`ArtistId`
            WHERE A.`EventId` = :eventId
            ORDER BY A.`StartDateTime`;"
        );

        $this->bind(":eventId", $eventId);

        return $this->processJazzActivitySet(
            $this->resultSet()
        );
    }

    public function getPerformanceByIdAndEventId($activityId, $eventId) {
        $this->query(
            "SELECT
                A.`ActivityId`,
                A.`Name` AS `ActivityName`,
                A.`StartDateTime`,
                A.`EndDateTime`,
                `Location`.`LocationId`,
                `Location`.`Name` AS `LocationName`,
                `Location`.`Seats` AS `LocationSeats`,
                `Hall`.`HallId`,
                `Hall`.`Name` AS `HallName`,
                `Hall`.`Seats` AS `HallSeats`,
                `Artist`.`ArtistId`,
                `Artist`.`Name` AS `ArtistName`,
                (
                    SELECT count(*)
                    FROM `SelectedTicket`
                    JOIN `Ticket` T on `SelectedTicket`.`TicketId` = T.`TicketId`
                    JOIN `TicketActivityLink` TAL on T.`TicketId` = TAL.`TicketId`
                    JOIN `Activity` A2 on TAL.`ActivityId` = A2.`ActivityId`
                    WHERE A.`ActivityId` = A2.`ActivityId`
                ) AS SeatsTaken
            FROM `Activity` AS A
            JOIN `Location` ON A.`LocationId` = `Location`.`LocationId`
            LEFT JOIN `Hall` ON A.`HallId` = `Hall`.`HallId`
            JOIN `ActivityArtistLink` ON `ActivityArtistLink`.`ActivityId` = A.`ActivityId`
            JOIN `Artist` ON `ActivityArtistLink`.`ArtistId` = `Artist`.`ArtistId`
            WHERE A.`ActivityId` = :activityId AND A.`EventId` = :eventId;"
        );

        $this->bind(":activityId", $activityId);
        $this->bind(":eventId", $eventId);

        return $this->processJazzActivitySet(
            $this->resultSet()
        )[0];
    }

    public function getJazzPerformancesOverview() {
        return $this->getPerformanceOverviewByEventId(1);
    }

    public function getJazzPerformanceById($activityId) {
        return $this->getPerformanceByIdAndEventId($activityId, 1);
    }

    public function getDancePerformancesOverview() {
        return $this->getPerformanceOverviewByEventId(4);
    }

    public function getDancePerformanceById($activityId) {
        return $this->getPerformanceByIdAndEventId($activityId, 4);
    }

    public function getFoodSessionsOverview() {
        $this->query(
            "SELECT
                A.`ActivityId`,
                A.`Name` AS `ActivityName`,
                A.`StartDateTime`,
                A.`EndDateTime`,
                `Location`.`LocationId`,
                `Location`.`Name` AS `LocationName`,
                `Location`.`Seats` AS `LocationSeats`
            FROM `Activity` AS A
            JOIN `Location` ON A.`LocationId` = `Location`.`LocationId`
            WHERE A.`EventId` = 2
            ORDER BY A.`StartDateTime`;"
        );

        $rows = $this->resultSet();

        return array_map(function ($row) {
            return new Activity(
                intval($row->ActivityId),
                $row->ActivityName,
                null,
                null,
                $this->parseDate($row->StartDateTime),
                $this->parseDate($row->EndDateTime),
                null,
                new Location(
                    intval($row->LocationId),
                    $row->LocationName,
                    null,
                    null,
                    null,
                    intval($row->LocationSeats),
                    null,
                    null,
                    null
                ),
                null,
                null,
                null
            );
        }, $rows);
    }

    public function getFoodSessionById($activityId) {
        $this->query(
            "SELECT
                A.`ActivityId`,
                A.`Name` AS `ActivityName`,
                A.`StartDateTime`,
                A.`EndDateTime`,
                `Location`.`LocationId`,
                `Location`.`Name` AS `LocationName`,
                `Location`.`Seats` AS `LocationSeats`
            FROM `Activity` AS A
            JOIN `Location` ON A.`LocationId` = `Location`.`LocationId`
            WHERE A.`EventId` = 2 AND `ActivityId` = :activityId
            ORDER BY A.`StartDateTime`;"
        );

        $this->bind(":activityId", $activityId);
        $row = $this->single();

        return new Activity(
            intval($row->ActivityId),
            $row->ActivityName,
            null,
            null,
            $this->parseDate($row->StartDateTime),
            $this->parseDate($row->EndDateTime),
            null,
            new Location(
                intval($row->LocationId),
                $row->LocationName,
                null,
                null,
                null,
                intval($row->LocationSeats),
                null,
                null,
                null
            ),
            null,
            null,
            null
        );
    }

    public function getHistoricToursOverview() {
        $this->query(
            "SELECT
                A.`ActivityId`,
                A.`Name` AS `ActivityName`,
                A.`StartDateTime`,
                A.`EndDateTime`,
                A.`Capacity`,
                `Language`,
                `Location`.`LocationId`,
                `Location`.`Name` AS `LocationName`,
                E.`EmployeeId`,
                E.`Name` AS `EmployeeName`
            FROM `Activity` AS A
            JOIN `Location` ON A.`LocationId` = `Location`.`LocationId`
            JOIN Employee E on A.EmployeeId = E.EmployeeId
            WHERE A.`EventId` = 3
            ORDER BY A.`StartDateTime`;"
        );

        $rows = $this->resultSet();

        return array_map(function ($row) {
            return new Activity(
                intval($row->ActivityId),
                $row->ActivityName,
                intval($row->Capacity),
                $row->Language,
                $this->parseDate($row->StartDateTime),
                $this->parseDate($row->EndDateTime),
                null,
                new Location(
                    intval($row->LocationId),
                    $row->LocationName,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ),
                null,
                new Employee(
                    intval($row->EmployeeId),
                    $row->EmployeeName,
                    null,
                    null,
                    null
                ),
                null
            );
        }, $rows);
    }

    public function getHistoricTourByIdMapped($activityId) {
        $this->query(
            "SELECT
                A.`ActivityId`,
                A.`Name` AS `ActivityName`,
                A.`StartDateTime`,
                A.`EndDateTime`,
                A.`Capacity`,
                `Language`,
                `Location`.`LocationId`,
                `Location`.`Name` AS `LocationName`,
                E.`EmployeeId`,
                E.`Name` AS `EmployeeName`
            FROM `Activity` AS A
            JOIN `Location` ON A.`LocationId` = `Location`.`LocationId`
            JOIN Employee E on A.EmployeeId = E.EmployeeId
            WHERE A.`EventId` = 3 AND `ActivityId` = :activityId
            ORDER BY A.`StartDateTime`;"
        );

        $this->bind(":activityId", $activityId);
        $row = $this->single();

        return new Activity(
            intval($row->ActivityId),
            $row->ActivityName,
            intval($row->Capacity),
            $row->Language,
            $this->parseDate($row->StartDateTime),
            $this->parseDate($row->EndDateTime),
            null,
            new Location(
                intval($row->LocationId),
                $row->LocationName,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ),
            null,
            new Employee(
                intval($row->EmployeeId),
                $row->EmployeeName,
                null,
                null,
                null
            ),
            null
        );
    }

    public function deleteActivityById($activityId) {
        $this->query(
            "DELETE FROM `Activity`
            WHERE `ActivityId` = :activityId"
        );

        $this->bind(":activityId", $activityId);
        $this->execute();
    }

    public function getCollidingLocationsCount($activityId, $startDateTime, $endDateTime, $locationId) {
        if ($activityId !== null) {
            $this->query(
                "SELECT `Activity`.`ActivityId`
                FROM `Activity`
                WHERE :startDateTime < `EndDateTime` AND
                      `StartDateTime` < :endDateTime AND
                      `LocationId` = :locationId AND
                      `ActivityId` != :activityId
                GROUP BY Activity.`ActivityId`;"
            );

            $this->bind(":activityId", $activityId);
        } else {
            $this->query(
                "SELECT `Activity`.`ActivityId`
                FROM `Activity`
                WHERE :startDateTime < `EndDateTime` AND
                      `StartDateTime` < :endDateTime AND
                      `LocationId` = :locationId
                GROUP BY Activity.`ActivityId`;"
            );
        }

        $this->bind(":startDateTime", $this->toDateString($startDateTime));
        $this->bind(":endDateTime", $this->toDateString($endDateTime));
        $this->bind(":locationId", $locationId);

        return count($this->resultSet());
    }

    public function getCollidingHallsCount($activityId, $startDateTime, $endDateTime, $hallId) {
        if ($activityId !== null) {
            $this->query(
                "SELECT `Activity`.`ActivityId`
                FROM `Activity`
                WHERE :startDateTime < `EndDateTime` AND
                      `StartDateTime` < :endDateTime AND
                      `HallId` = :hallId AND
                      `ActivityId` != :activityId
                GROUP BY Activity.`ActivityId`;"
            );

            $this->bind(":activityId", $activityId);
        } else {
            $this->query(
                "SELECT `Activity`.`ActivityId`
                FROM `Activity`
                WHERE :startDateTime < `EndDateTime` AND
                      `StartDateTime` < :endDateTime AND
                      `HallId` = :hallId
                GROUP BY Activity.`ActivityId`;"
            );
        }

        $this->bind(":startDateTime", $this->toDateString($startDateTime));
        $this->bind(":endDateTime", $this->toDateString($endDateTime));
        $this->bind(":hallId", $hallId);

        return count($this->resultSet());
    }

    public function getCollidingArtistCount($activityId, $startDateTime, $endDateTime, $artistIds) {
        $artistIdParams = join(", ", array_map(function ($i) { return ":" . $i; }, $artistIds));

        if ($activityId !== null) {
            $this->query(
                "SELECT `Activity`.`ActivityId`
                FROM `Activity`
                JOIN ActivityArtistLink AAL on Activity.ActivityId = AAL.ActivityId
                JOIN Artist A on A.ArtistId = AAL.ArtistId
                WHERE :startDateTime < `EndDateTime`AND
                      `StartDateTime` < :endDateTime AND
                      A.`ArtistId` IN ($artistIdParams) AND
                      Activity.`ActivityId` != :activityId
                GROUP BY Activity.`ActivityId`;"
            );

            $this->bind(":activityId", $activityId);
        } else {
            $this->query(
                "SELECT `Activity`.`ActivityId`
                FROM `Activity`
                JOIN ActivityArtistLink AAL on Activity.ActivityId = AAL.ActivityId
                JOIN Artist A on A.ArtistId = AAL.ArtistId
                WHERE :startDateTime < `EndDateTime`AND
                      `StartDateTime` < :endDateTime AND
                      A.`ArtistId` IN ($artistIdParams)
                GROUP BY Activity.`ActivityId`;"
            );
        }

        $this->bind(":startDateTime", $this->toDateString($startDateTime));
        $this->bind(":endDateTime", $this->toDateString($endDateTime));

        foreach ($artistIds as $artistId) {
            $this->bind(":" . $artistId, $artistId);
        }

        return count($this->resultSet());
    }

    private function processJazzActivitySet($set) {
        $activityIdMap = [];
        foreach ($set as $row) {
            $activityId = $row->ActivityId;

            if (!isset($activityIdMap[$activityId])) $activityIdMap[$activityId] = new Activity(
                intval($row->ActivityId),
                $row->ActivityName,
                null,
                null,
                $this->parseDate($row->StartDateTime),
                $this->parseDate($row->EndDateTime),
                null,
                new Location(
                    intval($row->LocationId),
                    $row->LocationName,
                    null,
                    null,
                    null,
                    intval($row->LocationSeats),
                    null,
                    null,
                    null
                ),
                $row->HallId === null ? null : new Hall(
                    intval($row->HallId),
                    $row->HallName,
                    intval($row->HallSeats)
                ),
                null,
                intval($row->SeatsTaken)
            );

            $activity = $activityIdMap[$activityId];
            $activity->addArtist(new Artist(
                intval($row->ArtistId),
                $row->ArtistName,
                null,
                null
            ));
        }

        return array_values($activityIdMap);
    }

    public function updateActivity($activityId, $name, $capacity, $language, $startDateTime, $endDateTime, $locationId, $hallId, $employeeId) {
        if ($startDateTime !== null) $startDateTime = $this->toDateString($startDateTime);
        if ($endDateTime !== null) $endDateTime = $this->toDateString($endDateTime);

        $this->query(
            "UPDATE `Activity`
            SET `Name` = :name,
                `Capacity` = :capacity,
                `Language` = :language,
                `StartDateTime` = :startDateTime,
                `EndDateTime` = :endDateTime,
                `LocationId` = :locationId,
                `HallId` = :hallId,
                `EmployeeId` = :employeeId
            WHERE `ActivityId` = :activityId"
        );

        $this->bind(":activityId", $activityId);
        $this->bind(":name", $name);
        $this->bind(":capacity", $capacity);
        $this->bind(":language", $language);
        $this->bind(":startDateTime", $startDateTime);
        $this->bind(":endDateTime", $endDateTime);
        $this->bind(":locationId", $locationId);
        $this->bind(":hallId", $hallId);
        $this->bind(":employeeId", $employeeId);

        $this->execute();
    }

    public function addActivity($eventId, $name, $capacity, $language, $startDateTime, $endDateTime, $locationId, $hallId, $employeeId) {
        if ($startDateTime !== null) $startDateTime = $this->toDateString($startDateTime);
        if ($endDateTime !== null) $endDateTime = $this->toDateString($endDateTime);

        $this->query(
            "INSERT INTO `Activity`
            VALUES (null, :name, :capacity, :language, :startDateTime, :endDateTime, :eventId, :locationId, :hallId, :employeeId)"
        );

        $this->bind(":eventId", $eventId);
        $this->bind(":name", $name);
        $this->bind(":capacity", $capacity);
        $this->bind(":language", $language);
        $this->bind(":startDateTime", $startDateTime);
        $this->bind(":endDateTime", $endDateTime);
        $this->bind(":locationId", $locationId);
        $this->bind(":hallId", $hallId);
        $this->bind(":employeeId", $employeeId);

        $this->execute();
    }

    public function deleteArtistLinksToActivity($activityId) {
        $this->query(
            "DELETE FROM `ActivityArtistLink`
            WHERE `ActivityId` = :activityId"
        );

        $this->bind(":activityId", $activityId);

        $this->execute();
    }

    public function createArtistLinkToActivity($activityId, $artistId) {
        $this->query(
            "INSERT INTO `ActivityArtistLink`
            VALUES (:activityId, :artistId)"
        );

        $this->bind(":activityId", $activityId);
        $this->bind(":artistId", $artistId);

        $this->execute();
    }

    public function getActivitiesConciseForEvent($eventId) {
        $this->query(
            "SELECT
                `ActivityId`,
                `Name`,
                `StartDateTime`,
                `EndDateTime`
            FROM `Activity`
            WHERE `EventId` = :eventId;"
        );

        $this->bind(":eventId", $eventId);

        return array_map(
            function ($row) {
                return new Activity(
                    intval($row->ActivityId),
                    $row->Name,
                    null,
                    null,
                    $this->parseDate($row->StartDateTime),
                    $this->parseDate($row->EndDateTime),
                    null,
                    null,
                    null,
                    null,
                    null
                );
            },
            $this->resultSet()
        );
    }
    //get (only) dance activities !
    public function getDanceTickets(){
        $this->query(
              "SELECT `Activity`.`Name`,
              `Activity`.`ActivityId`,
              date_format(`startDateTime`, '%H:%i') as 'StartDateTime',
              `Location`.`LocationId`,
              `Location`.`Address`,
              `Ticket`.`Price`
               FROM `Location`
               JOIN `Activity` ON `Activity`.`LocationId` = `Location`.`LocationId`
               JOIN `Ticket` ON `Activity`.`ActivityId` = `Ticket`.`TicketId`
               WHERE `Activity`.`EventId` = 4"  
        );
        $result = $this->resultSet();
        return $result;
      }



    // Get jazz tickets
    public function getJazzTickets(){
        $this->query(
            "SELECT `Ticket`.`TicketId`, 
            `Ticket`.`EventId`, 
            `Activity`.`ActivityId`, 
            `TicketActivityLink`.`ActivityId` AS `ActivityLink`, 
            `Price`, 
            `Ticket`.`Name` AS `TicketName`, 
            `Activity`.`Name` AS `ActivityName`, 
            `StartDateTime` AS `StartTime`, 
            `EndDateTime` AS `EndTime`, 
            `Activity`.`Capacity`,
            `Location`.`Name` AS `LocationName`, 
            `Hall`.`Name` AS `HallName`, 
            `Artist`.`Name` AS `ArtistName`, 
            `Artist`.`ImageId`, 
            `Artist`.`Description` AS `ArtistDescription`
            FROM `Activity`
            JOIN `ActivityArtistLink` ON `Activity`.`ActivityId` = `ActivityArtistLink`.`ActivityId`
            JOIN `Artist` ON `ActivityArtistLink`.`ArtistId` = `Artist`.`ArtistId`
            JOIN `Location` ON `Location`.`LocationId` = `Activity`.`LocationId`
            LEFT JOIN `Hall` ON `Hall`.`HallId` = `Activity`.`hallId`
            LEFT JOIN `Event` ON `Event`.`EventId` = `Activity`.`EventId`
            LEFT JOIN `TicketActivityLink` ON `Activity`.`ActivityId` = `TicketActivityLink`.`ActivityId` 
            LEFT JOIN `Ticket` ON `Ticket`.`TicketId` = `TicketActivityLink`.`TicketId`
            WHERE `Activity`.`EventId` = 1 OR `Ticket`.`EventId` = 1  
            UNION
            SELECT `Ticket`.`TicketId`, 
            `Ticket`.`EventId`, 
            `Activity`.`ActivityId`, 
            `TicketActivityLink`.`ActivityId` AS `ActivityLink`, 
            `Price`, 
            `Ticket`.`Name` AS `TicketName`, 
            `Activity`.`Name` AS `ActivityName`, 
            `StartDateTime` AS `StartTime`, 
            `EndDateTime` AS `EndTime`, 
            `Activity`.`Capacity`,
            `Location`.`Name` AS `LocationName`, 
            `Hall`.`Name` AS `HallName`, 
            `Artist`.`Name` AS `ArtistName`, 
            `Artist`.`ImageId`, 
            `Artist`.`Description` AS `ArtistDescription`
            FROM `Activity`
            JOIN `ActivityArtistLink` ON `Activity`.`ActivityId` = `ActivityArtistLink`.`ActivityId`
            JOIN `Artist` ON `ActivityArtistLink`.`ArtistId` = `Artist`.`ArtistId`
            JOIN `Location` ON `Location`.`LocationId` = `Activity`.`LocationId`
            LEFT JOIN `Hall` ON `Hall`.`HallId` = `Activity`.`hallId`
            LEFT JOIN `Event` ON `Event`.`EventId` = `Activity`.`EventId`
            RIGHT JOIN `TicketActivityLink` ON `Activity`.`ActivityId` = `TicketActivityLink`.`ActivityId` 
            RIGHT JOIN `Ticket`  ON `Ticket`.`TicketId` = `TicketActivityLink`.`TicketId`
            WHERE `Activity`.`EventId` = 1 OR `Ticket`.`EventId` = 1 
            ORDER BY `ActivityId` ASC;;"
        );
        $result = $this->resultSet();
        return $result;
    }

    // Get jazz ticket by id
    public function getJazzTicketById(int $id) {
        $this->query("SELECT `Ticket`.`TicketId`, 
        `Ticket`.`Name` AS `TicketName`, 
        `Price`, 
        `Activity`.`ActivityId`,
        `Capacity`,
        `StartDateTime`,
        `EndDateTime`
        FROM `Ticket` 
        LEFT JOIN `TicketActivityLink` ON `Ticket`.`TicketId` = `TicketActivityLink`.`TicketId` 
        LEFT JOIN `Activity`  ON `Activity`.`ActivityId` = `TicketActivityLink`.`ActivityId`
        WHERE `Ticket`.`TicketId` = :id;
        ");
        $this->bind(':id',$id);
        $result = $this->single();
        if(isset($result->Capacity)) {
            if($result > 0) {
                return $result;
            } else
            if($result < 1) {
                return 'empty';
            } else {
                return false;
            }
        } else {
            return $result;
        }

    }
    
    public function getReservations() {
        $this->query(
            "SELECT *
               FROM `Activity`
               WHERE `EventId`=2"
            );
        
        $reservationList = null;
        
        foreach($this->resultSet() as $reservation) {
            $reservationList[] = new Activity(
                $reservation->ActivityId,
                $reservation->Name,
                $reservation->Capacity,
                null,
                $reservation->StartDateTime,
                $reservation->EndDateTime,
                null,
                $reservation->LocationId,
                null,
                null,
                null
                );
        }
        return $reservationList;
        
    }
}
