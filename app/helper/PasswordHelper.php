<?php
require_once(APPROOT."/./helper/UtilHelper.php");

abstract class PasswordHelper {
    public static function hash($password) {
        $salt = UtilHelper::randomString(16);
        $hash = self::hashWithSalt($password, $salt);

        return $hash . "|" . $salt;
    }

    public static function check($password, $hash) {
        // The password consists of two parts separated by a '|'. The first part is the actual hash and the second part
        // is the salt used to create the hash.
        $hashParts = explode("|", $hash);
        $passwordHash = $hashParts[0];
        $salt = $hashParts[1];

        $checkHash = self::hashWithSalt($password, $salt);
        return $passwordHash === $checkHash;
    }

    private static function hashWithSalt($password, $salt) {
        return hash("sha256", $password . $salt);
    }

    public static function isStrongPassword($password) {
        if (strlen($password) < 8) return false;
        if (!preg_match("#[0-9]+#", $password)) return false;
        if (!preg_match("#[a-zA-Z]+#", $password)) return false;

        return true;
    }
}