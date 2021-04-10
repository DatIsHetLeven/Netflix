<?php

class Invoice {
    private $id;
    private $purchaseDate;
    private $status;
    private $selectedTickets;
    private $customer;

    public function __construct($id, $purchaseDate, $status, $customer) {
        $this->id = $id;
        $this->purchaseDate = $purchaseDate;
        $this->status = $status;
        $this->customer = $customer;

        $this->selectedTickets = [];
    }

    public function getId() { return $this->id; }
    public function getPurchaseDate() { return $this->purchaseDate; }
    public function getStatus() { return $this->status; }
    public function getCustomer() { return $this->customer; }
    public function getSelectedTickets() { return $this->selectedTickets; }
    public function getSubTotal() {
        return array_reduce(
            $this->selectedTickets,
            function ($acc, $ticket) {
                return $acc + $ticket->getSelectedTicketPrice();
            },
            0
        );
    }

    public function addSelectedTicket($selectedTicket) {
        $this->selectedTickets[] = $selectedTicket;
    }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "purchaseDate" => $this->getPurchaseDate(),
            "status" => $this->getStatus(),
            "customer" => $this->getCustomer()->getJsonArray(),
            "selectedTickets" => array_map(
                function ($selectedTicket) {
                    return $selectedTicket->getJsonArray();
                },
                $this->getSelectedTickets()
            ),
            "revenue" => $this->getSubTotal(),
        ];
    }
}