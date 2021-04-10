<?php

class Activity {
    private $id;
    private $name;
    private $capacity;
    private $language;
    private $startDateTime;
    private $endDateTime;
    private $event;
    private $location;
    private $hall;
    private $employee;
    private $seatsTaken;
    private $artists;

    public function __construct($id, $name, $capacity, $language, $startDateTime, $endDateTime, $event, $location, $hall, $employee, $seatsTaken) {
        $this->id = $id;
        $this->name = $name;
        $this->capacity = $capacity;
        $this->language = $language;
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->event = $event;
        $this->location = $location;
        $this->hall = $hall;
        $this->employee = $employee;
        $this->seatsTaken = $seatsTaken;
        $this->artists = [];
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getCapacity() { return $this->capacity; }
    public function getLanguage() { return $this->language; }
    public function getStartDateTime() { return $this->startDateTime; }
    public function getEndDateTime() { return $this->endDateTime; }
    public function getEvent() { return $this->event; }
    public function getLocation() { return $this->location; }
    public function getHall() { return $this->hall; }
    public function getEmployee() { return $this->employee; }
    public function getArtists() { return $this->artists; }
    public function getSeatsTaken() { return $this->seatsTaken; }

    public function addArtist($artist) {
        $this->artists[] = $artist;
    }

    public function getJsonArray() {
        $jsonArray = [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "capacity" => $this->getCapacity(),
            "language" => $this->getLanguage(),
            "startDateTime" => $this->getStartDateTime(),
            "endDateTime" => $this->getEndDateTime(),
            "event" => null,
            "location" => null,
            "hall" => null,
            "employee" => null,
            "artists" => array_map(function ($artist) { return $artist->getJsonArray(); }, $this->getArtists()),
            "seatsTaken" => $this->getSeatsTaken()
        ];

        if ($this->getEvent() !== null) $jsonArray["event"] = $this->getEvent()->getJsonArray();
        if ($this->getLocation() !== null) $jsonArray["location"] = $this->getLocation()->getJsonArray();
        if ($this->getHall() !== null) $jsonArray["hall"] = $this->getHall()->getJsonArray();
        if ($this->getEmployee() !== null) $jsonArray["employee"] = $this->getEmployee()->getJsonArray();

        return $jsonArray;
    }
}