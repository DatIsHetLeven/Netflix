<?php

class Event {
    private $id;
    private $name;
    private $shortName;
    private $description;
    private $imageId;

    public function __construct($id, $name, $shortName, $description, $imageId) {
        $this->id = $id;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->imageId = $imageId;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getShortName() { return $this->shortName; }
    public function getDescription() { return $this->description; }
    public function getImageId() { return $this->imageId; }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "shortName" => $this->getShortName(),
            "description" => $this->getDescription(),
            "imageId" => $this->getImageId()
        ];
    }
}