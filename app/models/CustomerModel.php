<?php
require_once(APPROOT . "/classes/Customer.php");

class CustomerModel extends Database {
    public function getAll(){
        $this->query('SELECT * FROM `Customer`');
        $result = $this->resultSet();

        return $result;
    }

    public function getByEmailAddress($emailAddress) {
        $this->query(
            "SELECT *
            FROM `Customer`
            WHERE `EmailAddress` = :emailAddress"
        );
        $this->bind(":emailAddress", $emailAddress);

        $result = $this->single();
        if ($result === false) return null;

        return $this->processSingle($result);
    }

    public function createCustomer($name, $emailAddress, $passwordHash) {
        $this->query(
            "INSERT INTO `Customer`
            VALUES (null, :name, :emailAddress, :passwordHash)"
        );

        $this->bind(":name", $name);
        $this->bind(":emailAddress", $emailAddress);
        $this->bind(":passwordHash", $passwordHash);

        $this->execute();
    }

    public function deleteCustomerById($id) {
        $this->query(
            "DELETE FROM `Customer`
            WHERE `CustomerId` = :id;"
        );

        $this->bind(":id", $id);
        $this->execute();
    }

    public function updateCustomerPassword($customerId, $passwordHash) {
        $this->query(
            "UPDATE `Customer`
            SET `Password` = :passwordHash
            WHERE `CustomerId` = :customerId
        ");

        $this->bind(":passwordHash", $passwordHash);
        $this->bind(":customerId", $customerId);

        $this->execute();
    }

    public function updateAccountDetails($customerId, $name, $emailAddress, $passwordHash) {
        $this->query(
            "UPDATE `Customer`
            SET `Name` = :name,
                `EmailAddress` = :emailAddress,
                `Password` = :passwordHash
            WHERE `CustomerId` = :customerId
        ");

        $this->bind(":name", $name);
        $this->bind(":emailAddress", $emailAddress);
        $this->bind(":passwordHash", $passwordHash);
        $this->bind(":customerId", $customerId);

        $this->execute();
    }

    private function processMultiple($set) {
        return array_map(function ($row) { return $this->processSingle($row); }, $set);
    }

    private function processSingle($item) {
        return new Customer(
            $item->CustomerId,
            $item->Name,
            $item->EmailAddress,
            $item->Password
        );
    }
}