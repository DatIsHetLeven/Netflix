<?php

class Employee {
    private $id;
    private $name;
    private $emailAddress;
    private $password;
    private $userRole;

    public function __construct($id, $name, $emailAddress, $password, $userRole) {
        $this->id = $id;
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->password = $password;
        $this->userRole = $userRole;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmailAddress() { return $this->emailAddress; }
    public function getPassword() { return $this->password; }
    public function getUserRole() { return $this->userRole; }

    public function getJsonArray() {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "emailAddress" => $this->getEmailAddress(),
            "userRole" => $this->getUserRole(),
        ];
    }
}