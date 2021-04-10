<?php

class InvoiceOverview {
    private $id;
    private $status;
    private $purchaseDate;
    private $subTotal;
    private $numberOfTickets;

    public function __construct($id, $status, $purchaseDate, $subTotal, $numberOfTickets) {
        $this->id = $id;
        $this->status = $status;
        $this->purchaseDate = $purchaseDate;
        $this->subTotal = $subTotal;
        $this->numberOfTickets = $numberOfTickets;
    }

    public function getId() { return $this->id; }
    public function getStatus() { return $this->status; }
    public function getPurchaseDate() { return $this->purchaseDate; }
    public function getSubTotal() { return $this->subTotal; }
    public function getNumberOfTickets() { return $this->numberOfTickets; }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "status" => $this->getStatus(),
            "purchaseDate" => $this->getPurchaseDate(),
            "subTotal" => $this->getSubTotal(),
            "numberOfTickets" => $this->getNumberOfTickets(),
        ];
    }
}