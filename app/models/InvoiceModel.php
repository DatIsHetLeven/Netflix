<?php
require_once APPROOT."/classes/InvoiceOverview.php";
require_once APPROOT."/classes/Invoice.php";
require_once APPROOT."/classes/Customer.php";
require_once APPROOT."/classes/SelectedTicket.php";

class InvoiceModel extends Database {
    public function makeInvoiceId() {
        $this->query("SELECT MAX(InvoiceId) AS id FROM Invoice");
        $result = $this->single();

        return $result;
    }

    public function createInvoice($invoiceId, $customerid, $purchaseDate) {
        $this->query('INSERT INTO `Invoice`(`invoiceId`,`PurchaseDate`, `PaymentStatus`, `CustomerId`) VALUES (:id, :PurchaseDate, :PaymentStatus, :CustomerId)');

        $this->bind(':id', $invoiceId);
        $this->bind(':PurchaseDate', $purchaseDate);
        $this->bind(':PaymentStatus', 'unpaid');
        $this->bind(':CustomerId', $customerid);

        $this->execute();
    }

    public function getInvoice($id){
        $this->query('SELECT * FROM  `Invoice` WHERE 
        `invoiceId` = :id');

        $this->bind(':id', $id);
        return $this->single();
    }

    public function getInvoiceOverview() {
        $this->query(
            "SELECT *,
                (
                    SELECT SUM(`Price`)
                    FROM `SelectedTicket`
                    WHERE `SelectedTicket`.`InvoiceId` = `Invoice`.`InvoiceId`
                ) AS `TotalPrice`,
               (
                   SELECT COUNT(*)
                   FROM `SelectedTicket`
                   WHERE `SelectedTicket`.`InvoiceId` = `Invoice`.`InvoiceId`
               ) AS `NumberOfTickets`
            FROM `Invoice`;"
        );

        return array_map(function ($row) {
            return new InvoiceOverview(
                intval($row->InvoiceId),
                $row->PaymentStatus,
                $this->parseDate($row->PurchaseDate),
                floatval($row->TotalPrice),
                intval($row->NumberOfTickets)
            );
        }, $this->resultSet());
    }

    public function getInvoiceById($invoiceId) {
        $this->query(
            "SELECT
                `Invoice`.`InvoiceId`,
                `PurchaseDate`,
                `PaymentStatus`,
                C.`CustomerId`,
                C.`Name` AS `CustomerName`,
                `EmailAddress`,
                `SelectedTicketId`,
                ST.Price AS `SelectedTicketPrice`,
                `Participants`,
                `Comment`,
                T.`TicketId`,
                T.`Name` AS `TicketName`,
                T.Price AS `TicketPrice`
            FROM Invoice
            LEFT JOIN Customer C on Invoice.CustomerId = C.CustomerId
            JOIN SelectedTicket ST on Invoice.InvoiceId = ST.InvoiceId
            LEFT JOIN Ticket T on ST.TicketId = T.TicketId;"
        );

        $this->bind(":invoiceId", $invoiceId);

        return $this->mapInvoiceRows(
            $this->resultSet()
        )[0];
    }

    private function mapInvoiceRows($rows) {
        $invoiceIdMap = [];

        foreach ($rows as $row) {
            $invoiceId = intval($row->InvoiceId);

            if (!isset($invoiceIdMap[$invoiceId])) {
                $customer = null;

                if ($row->EmailAddress !== null) {
                    $customer = new Customer(
                        $row->CustomerId,
                        $row->CustomerName,
                        $row->EmailAddress,
                        null
                    );
                } else {
                    $customer = new Customer(
                        null,
                        "(Deleted customer)",
                        "(Deleted customer)",
                        null
                    );
                }

                $invoiceIdMap[$invoiceId] = new Invoice(
                    $invoiceId,
                    $this->parseDate($row->PurchaseDate),
                    $row->PaymentStatus,
                    $customer
                );
            }

            $invoice = $invoiceIdMap[$invoiceId];

            if ($row->TicketId !== null) {
                $invoice->addSelectedTicket(
                    new SelectedTicket(
                        intval($row->SelectedTicketId),
                        floatval($row->SelectedTicketPrice),
                        intval($row->Participants),
                        $row->Comment,
                        intval($row->TicketId),
                        $row->TicketName,
                        floatval($row->TicketPrice)
                    )
                );
            } else {
                $invoice->addSelectedTicket(
                    new SelectedTicket(
                        intval($row->SelectedTicketId),
                        floatval($row->SelectedTicketPrice),
                        intval($row->Participants),
                        $row->Comment,
                        null,
                        "(Deleted ticket)",
                        0
                    )
                );
            }
        }

        return array_values($invoiceIdMap);
    }

    public function updateStatusInvoice($id, $status) {
      $this->query(
        "UPDATE `Invoice`
          SET `PaymentStatus` = :paymentStatus
          WHERE `InvoiceId` = :invoiceId
      ");
      $this->bind(":invoiceId", $id);
      $this->bind(":paymentStatus", $status);

      
        if($this->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
