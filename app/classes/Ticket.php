<?php

class Ticket {
    private $id;
    private $name;
    private $price;
    private $activities;

    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;

        $this->activities = [];
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getPrice() { return $this->price; }
    public function getActivities() { return $this->activities; }

    public function addActivity($activity) {
        $this->activities[] = $activity;
    }

    public function getTicketType() {
        // If there is only one activity, it's a single ticket
        if (count($this->activities) === 1) {
            return "Single";
        }

        // Map the dates to an array
        $dateList = [];
        foreach ($this->activities as $activity) {
            $startDateString = substr($activity->getStartDateTime(), 0, 11);
            $dateList[$startDateString] = true;
        }

        // If there are multiple keys, the ticket spans multiple days
        $numberOfDays = count(array_keys($dateList));
        return $numberOfDays === 1 ? "Daypass" : "All access";
    }
    

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "price" => $this->getPrice(),
            "activities" => array_map(
                function ($activity) { return $activity->getJsonArray(); },
                $this->getActivities()
            )
        ];
    }
}