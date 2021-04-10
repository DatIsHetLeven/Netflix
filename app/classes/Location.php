<?php

class Location {
    private $id;
    private $name;
    private $description;
    private $imageId;
    private $address;
    private $seats;
    private $genre;

    private $eventId;
    
    private $isUsed;
    private $halls;
    
    private $stars;
    private $kidsMenu;

    public function __construct($id, $name, $description, $imageId, $address, $seats, $genre) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->imageId = $imageId;
        $this->address = $address;
        $this->seats = $seats;
        $this->genre = $genre;

        $this->isUsed = null;
        $this->halls = [];
        
        $this->eventId = null;
        
        $this->stars = null;
        $this->kidsMenu = null;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getImageId() { return $this->imageId; }
    public function getAddress() { return $this->address; }
    public function getSeats() { return $this->seats; }
    public function getGenre() { return $this->genre; }
    public function getIsUsed() { return $this->isUsed; }
    public function getHalls() { return $this->halls; }
    public function getStars() { return $this->stars; }
    public function getKidsMenu() { return $this->kidsMenu; }
    public function getEventId() { return $this->eventId; }

    public function setIsUsed($value) {
        $this->isUsed = $value;
    }

    public function addHall($hall) {
        $this->halls[] = $hall;
    }
    
    public function setStars($amount) {
        $this->stars = $amount;
    }
    
    public function setKidsMenu($kidsBit) {
        if($kidsBit === "1") {
            $this->kidsMenu = "true";
        } else {
            $this->kidsMenu = "false";
        }
    }
    
    public function setEventId($id) {
        $this->eventId = $id;
    }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "description" => $this->getDescription(),
            "imageId" => $this->getImageId(),
            "address" => $this->getAddress(),
            "seats" => $this->getSeats(),
            "genre" => $this->getGenre(),
            "isUsed" => $this->getIsUsed(),
            "halls" => array_map(function ($hall) { return $hall->getJsonArray(); }, $this->getHalls())
        ];
    }
}