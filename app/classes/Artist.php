<?php

class Artist {
    private $id;
    private $name;
    private $imageId;
    private $description;

    public function __construct($id, $name, $imageId, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->imageId = $imageId;
        $this->description = $description;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getImageId() { return $this->imageId; }
    public function getDescription() { return $this->description; }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "imageId" => $this->getImageId(),
            "description" => $this->getDescription()
        ];
    }
}