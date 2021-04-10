<?php
require_once APPROOT . "/classes/Employee.php";

class EmployeeModel extends Database {
    public function getAll() {
        $this->query(
            "SELECT *
            FROM `Employee`"
        );

        return $this->processMultiple($this->resultSet());
    }

    public function getById($employeeId) {
        $this->query(
            "SELECT *
            FROM `Employee`
            WHERE `EmployeeId` = :employeeId"
        );

        $this->bind(":employeeId", $employeeId);
        return $this->processSingle($this->single());
    }

    public function getEmployeeByEmailAddress($emailAddress) {
        $this->query("SELECT *
        FROM `Employee`
        WHERE `EmailAddress` = :emailAddress");

        $this->bind(":emailAddress", $emailAddress);
        return $this->processSingle($this->single());
    }

    public function createEmployee($name, $emailAddress, $passwordHash, $userRole) {
        $this->query(
            "INSERT INTO `Employee`
            VALUES (null, :name, :emailAddress, :passwordHash, :userRole)"
        );

        $this->bind(":name", $name);
        $this->bind(":emailAddress", $emailAddress);
        $this->bind(":passwordHash", $passwordHash);
        $this->bind(":userRole", $userRole);

        $this->execute();
    }

    public function updateEmployee($employeeId, $name, $emailAddress, $userRole) {
        $this->query(
            "UPDATE `Employee`
            SET `Name` = :name,
                `EmailAddress` = :emailAddress,
                `UserRole` = :userRole
            WHERE `EmployeeId` = :employeeId"
        );

        $this->bind(":employeeId", $employeeId);
        $this->bind(":name", $name);
        $this->bind(":emailAddress", $emailAddress);
        $this->bind(":userRole", $userRole);

        $this->execute();
    }

    public function deleteEmployeeById($employeeId) {
        $this->query(
            "DELETE FROM `Employee`
            WHERE `EmployeeId` = :employeeId"
        );

        $this->bind(":employeeId", $employeeId);
        $this->execute();
    }

    public function updateEmployeePassword($employeeId, $passwordHash) {
        $this->query(
            "UPDATE `Employee`
            SET `Password` = :passwordHash
            WHERE `EmployeeId` = :employeeId"
        );

        $this->bind(":employeeId", $employeeId);
        $this->bind(":passwordHash", $passwordHash);

        $this->execute();
    }

    private function processMultiple($set) {
        return array_map(function ($row) { return $this->processSingle($row); }, $set);
    }

    private function processSingle($item) {
        if (!$item) return null;

        return new Employee(
            $item->EmployeeId,
            $item->Name,
            $item->EmailAddress,
            $item->Password,
            $item->UserRole
        );
    }
}