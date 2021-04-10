<?php

class Hall {
    private $id;
    private $name;
    private $seats;

    private $isUsed;

    public function __construct($id, $name, $seats) {
        $this->id = $id;
        $this->name = $name;
        $this->seats = $seats;

        $this->isUsed = null;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSeats() { return $this->seats; }
    public function getIsUsed() { return $this->isUsed; }

    public function setIsUsed($value) {
        $this->isUsed = $value;
    }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "seats" => $this->getSeats(),
            "isUsed" => $this->getIsUsed(),
        ];
    }
}