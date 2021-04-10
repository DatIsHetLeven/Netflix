<?php

abstract class EmployeeSessionHelper {
    const roleValueMap = [
        "volunteer" => 1,
        "editor" => 2,
        "admin" => 3,
        "system" => 4
    ];

    public static function currentEmployee() {
        return $_SESSION["loggedInEmployee"];
    }

    public static function guard($role = null) {
        if (!isset(self::roleValueMap[$role])) throw new Exception("Invalid role of '$role'");

        // Check if there is an employee session
        $loggedInEmployee = self::currentEmployee();
        if ($loggedInEmployee === null) return false;

        // Check if the employee role is lower than the required guard
        $currentRoleValue = self::roleValueMap[$loggedInEmployee->getUserRole()];
        $requiredRoleValue = self::roleValueMap[$role];
        if ($currentRoleValue < $requiredRoleValue) return false;

        return true;
    }

    public static function guardPage($role = null) {
        if (!self::guard($role)) {
            header("Location: " . URLROOT . "/cms/login");
            exit();
        }
    }
}