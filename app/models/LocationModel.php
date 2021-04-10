<?php
require_once APPROOT . "/classes/Location.php";
require_once APPROOT . "/classes/Hall.php";

class LocationModel extends Database {
    
    public function getRestaurants() {
        $this->query("SELECT * 
                      FROM `Location` 
                      WHERE `EventId`=2");
        $restaurants = [];
        
        foreach($this->resultSet() as $restaurant) {
            $locationToAdd = new Location(
                    $restaurant->LocationId,
                    $restaurant->Name,
                    $restaurant->Description,
                    $restaurant->ImageId,
                    $restaurant->Address,
                    $restaurant->Seats,
                    $restaurant->Genre
                );
            $locationToAdd->setStars($restaurant->Stars);
            $locationToAdd->setKidsMenu($restaurant->KidsMenu);
            $locationToAdd->setEventId($restaurant->EventId);
            
            $restaurants[] = $locationToAdd;
            
        }
        
        return $restaurants;
        
    }
    
    public function getOverviewByEventId($eventId) {
        $this->query(
            "SELECT 
                `LocationId`,
                `Name`
            FROM `Location`
            WHERE `LocationId` IN (
                SELECT `LocationId`
                FROM `Activity`
                WHERE `EventId` = :eventId
            );"
        );

        $this->bind(":eventId", $eventId);

        return array_map(function ($row) {
            return new Location(
                $row->LocationId,
                $row->Name,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            );
        }, $this->resultSet());
    }

    public function getLocationsByEventId($eventId) {
        $this->query(
            "SELECT
                *,
                `Location`.`LocationId` AS `LocationId`,
                `Location`.`Name` AS `LocationName`,
                `Location`.`Description`,
                `Location`.`Seats` AS `LocationSeats`,
                H.`Name` AS `HallName`,
                H.`Seats` AS `HallSeats`,
                (
                    SELECT count(*)
                    FROM `Activity`
                    WHERE `Activity`.`HallId` = H.`HallId`
                ) AS `HallsUsedByActivities`,
                (
                    SELECT count(*)
                    FROM `Activity`
                    WHERE `Activity`.`LocationId` = H.`LocationId`
                ) AS `LocationUsedByActivities`
            FROM `Location`
            LEFT JOIN Hall H on Location.LocationId = H.LocationId
            WHERE `EventId` = :eventId;"
        );

        $this->bind(":eventId", $eventId);

        return $this->mapFullLocationResponse($this->resultSet());
    }

    public function getLocationById($locationId) {
        $this->query(
            "SELECT
                *,
                `Location`.`LocationId` AS `_LocationId`,
                `Location`.`Name` AS `LocationName`,
                `Location`.`Description`,
                `Location`.`Seats` AS `LocationSeats`,
                H.`Name` AS `HallName`,
                H.`Seats` AS `HallSeats`,
                (
                    SELECT count(*)
                    FROM `Activity`
                    WHERE `Activity`.`HallId` = H.`HallId`
                ) AS `HallsUsedByActivities`,
                (
                    SELECT count(*)
                    FROM `Activity`
                    WHERE `Activity`.`LocationId` = `Location`.`LocationId`
                ) AS `LocationUsedByActivities`
            FROM `Location`
            LEFT JOIN Hall H on Location.LocationId = H.LocationId
            WHERE Location.`LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);

        return $this->mapFullLocationResponse($this->resultSet())[0];
    }

    public function updateLocation($locationId, $name, $description, $imageId, $address, $seats, $genre, $stars, $hasKidsMenu) {
        $this->query(
            "UPDATE `Location`
            SET `Name` = :name,
                `Description` = :description,
                `ImageId` = :imageId,
                `Address` = :address,
                `Seats` = :seats,
                `Genre` = :genre,
                `Stars` = :stars,
                `KidsMenu` = :hasKidsMenu
            WHERE `LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);
        $this->bind(":name", $name);
        $this->bind(":description", $description);
        $this->bind(":imageId", $imageId);
        $this->bind(":address", $address);
        $this->bind(":seats", $seats);
        $this->bind(":genre", $genre);
        $this->bind(":stars", $stars);
        $this->bind(":hasKidsMenu", $hasKidsMenu);

        $this->execute();
    }

    public function updateSeats($locationId, $seats) {
        $this->query(
            "UPDATE `Location`
            SET `Seats` = :seats
            WHERE `LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);
        $this->bind(":seats", $seats);

        $this->execute();
    }

    public function updateGenres($locationId, $genre) {
        $this->query(
            "UPDATE `Location`
            SET `Genre` = :genre
            WHERE `LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);
        $this->bind(":genre", $genre);

        $this->execute();
    }

    public function updateStars($locationId, $stars) {
        $this->query(
            "UPDATE `Location`
            SET `Stars` = :stars
            WHERE `LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);
        $this->bind(":stars", $stars);

        $this->execute();
    }

    public function updateHasKidsMenu($locationId, $hasKidsMenu) {
        $this->query(
            "UPDATE `Location`
            SET `KidsMenu` = :hasKidsMenu
            WHERE `LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);
        $this->bind(":hasKidsMenu", $hasKidsMenu);

        $this->execute();
    }

    public function createLocation($eventId, $name, $description, $imageId, $address, $seats, $genre, $stars, $hasKidsMenu) {
        $this->query(
            "INSERT INTO `Location`
            VALUES (null, :name, :description, :imageId, :address, :seats, :genre, :stars, :hasKidsMenu, :eventId)"
        );

        $this->bind(":eventId", $eventId);
        $this->bind(":name", $name);
        $this->bind(":description", $description);
        $this->bind(":imageId", $imageId);
        $this->bind(":address", $address);
        $this->bind(":seats", $seats);
        $this->bind(":genre", $genre);
        $this->bind(":stars", $stars);
        $this->bind(":hasKidsMenu", $hasKidsMenu);

        $this->execute();
    }

    public function deleteLocation($locationId) {
        $this->query(
            "DELETE FROM `Location`
            WHERE `LocationId` = :locationId;"
        );

        $this->bind(":locationId", $locationId);

        $this->execute();
    }

    private function mapFullLocationResponse($rows) {
        $locationIdMap = [];

        foreach ($rows as $row) {
            $locationId = $row->LocationId;

            if (!isset($locationIdMap[$locationId])) {
                $locationIdMap[$locationId] = new Location(
                    intval($row->LocationId),
                    $row->LocationName,
                    $row->Description,
                    $row->ImageId,
                    $row->Address,
                    intval($row->LocationSeats),
                    $row->Genre,
                    $row->Stars,
                    $row->KidsMenu === "1"
                );

                $locationIdMap[$locationId]->setIsUsed($row->LocationUsedByActivities > 0);
            }

            if ($row->HallId !== null) {
                $location = $locationIdMap[$locationId];

                $hall = new Hall(
                    intval($row->HallId),
                    $row->HallName,
                    intval($row->HallSeats)
                );

                $hall->setIsUsed($row->HallsUsedByActivities > 0);

                $location->addHall($hall);
            }
        }

        return array_values($locationIdMap);
    }
}