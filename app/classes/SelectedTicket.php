<?php
require_once APPROOT."/classes/Ticket.php";

class SelectedTicket extends Ticket {
    private $selectedTicketId;
    private $selectedTicketPrice;
    private $participants;
    private $comment;

    public function __construct($selectedTicketId, $selectedTicketPrice, $participants, $comment, $ticketId, $name, $price) {
        parent::__construct($ticketId, $name, $price);

        $this->selectedTicketId = $selectedTicketId;
        $this->selectedTicketPrice = $selectedTicketPrice;
        $this->participants = $participants;
        $this->comment = $comment;
    }

    public function getSelectedTicketId() { return $this->selectedTicketId; }
    public function getSelectedTicketPrice() { return $this->selectedTicketPrice; }
    public function getParticipants() { return $this->participants; }
    public function getComment() { return $this->comment; }

    public function getJsonArray() {
        $jsonArray = parent::getJsonArray();

        $jsonArray["selectedTicketId"] = $this->getSelectedTicketId();
        $jsonArray["selectedTicketPrice"] = $this->getSelectedTicketPrice();
        $jsonArray["participants"] = $this->getParticipants();
        $jsonArray["comment"] = $this->getComment();

        return $jsonArray;
    }
}