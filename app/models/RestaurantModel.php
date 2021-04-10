<?php

class RestaurantModel extends Database {

    public function getRestaurants(){

        $this->query("SELECT * FROM `location`");
        $result = $this->resultSet();

        return $result;
    }
}
?>