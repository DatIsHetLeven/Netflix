<?php

abstract class QueryStringHelper {
    public static function createQueryString($array) {
        if (count($array) === 0) return "";
        $sections = [];

        foreach ($array as $key => $value) {
            $sections[] = $key . "=" . urlencode($value);
        }

        return "?" . join("&", $sections);
    }
}