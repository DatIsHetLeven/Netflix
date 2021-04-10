<?php

class HallModel extends Database {
    public function createHall($locationId, $name, $seats) {
        $this->query(
            "INSERT INTO `Hall`
            VALUES (null, :name, :seats, :locationId);"
        );
        $this->bind(":locationId", $locationId);
        $this->bind(":name", $name);
        $this->bind(":seats", $seats);

        $this->execute();
    }

    public function updateHall($hallId, $name, $seats) {
        $this->query(
            "UPDATE `Hall`
            SET `Name` = :name,
                `Seats` = :seats
            WHERE `HallId` = :hallId;"
        );

        $this->bind(":hallId", $hallId);
        $this->bind(":name", $name);
        $this->bind(":seats", $seats);

        $this->execute();
    }

    public function deleteHall($hallId) {
        $this->query(
            "DELETE FROM `Hall`
            WHERE `HallId` = :hallId;"
        );

        $this->bind(":hallId", $hallId);

        $this->execute();
    }
}