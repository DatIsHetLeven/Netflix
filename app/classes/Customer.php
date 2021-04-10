<?php

class Customer {
    private $id;
    private $name;
    private $emailAddress;
    private $password;

    public function __construct($id, $name, $emailAddress, $password) {
        $this->id = $id;
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->password = $password;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmailAddress() { return $this->emailAddress; }
    public function getPassword() { return $this->password; }

    public function getIsGuest() {
        return $this->getPassword() === null;
    }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "emailAddress" => $this->getEmailAddress(),
            "isGuestAccount" => $this->getIsGuest(),
        ];
    }
}