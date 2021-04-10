<?php

class Page {
    private $id;
    private $title;
    private $jsonContentString;

    public function __construct($id, $title, $jsonContentString) {
        $this->id = $id;
        $this->title = $title;
        $this->jsonContentString = $jsonContentString;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getJsonContentString() { return $this->jsonContentString; }

    public function getJsonBlocks() {
        return json_decode($this->getJsonContentString(), true);
    }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "jsonBlocks" => $this->getJsonBlocks(),
        ];
    }
}