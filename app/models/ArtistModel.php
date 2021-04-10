<?php
require_once APPROOT . "/classes/Artist.php";

class ArtistModel extends Database {
    public function getOverviewByEventId($eventId) {
        $this->query(
            "SELECT *
            FROM `Artist`
            WHERE `EventId` = :eventId;"
        );

        $this->bind(":eventId", $eventId);

        return array_map(function ($row) {
            return new Artist(
                $row->ArtistId,
                $row->Name,
                $row->ImageId,
                $row->Description
            );
        }, $this->resultSet());
    }

    public function getArtistById($artistId) {
        $this->query(
            "SELECT *
            FROM `Artist`
            WHERE `ArtistId` = :artistId;"
        );

        $this->bind(":artistId", $artistId);

        $row = $this->single();
        return new Artist(
            $row->ArtistId,
            $row->Name,
            $row->ImageId,
            $row->Description
        );
    }

    public function createArtist($eventId, $name, $imageId, $description) {
        $this->query(
            "INSERT INTO `Artist`
            VALUES (null, :name, :imageId, :description, :eventId);"
        );

        $this->bind(":eventId", $eventId);
        $this->bind(":name", $name);
        $this->bind(":imageId", $imageId);
        $this->bind(":description", $description);

        $this->execute();
    }

    public function updateArtist($artistId, $name, $imageId, $description) {
        $this->query(
            "UPDATE `Artist`
            SET `Name` = :name,
                `ImageId` = :imageId,
                `Description` = :description
            WHERE `ArtistId` = :artistId"
        );

        $this->bind(":artistId", $artistId);
        $this->bind(":name", $name);
        $this->bind(":imageId", $imageId);
        $this->bind(":description", $description);

        $this->execute();
    }

    public function deleteArtist($artistId) {
        $this->query(
            "DELETE FROM `Artist`
            WHERE `ArtistId` = :artistId"
        );

        $this->bind(":artistId", $artistId);

        $this->execute();
    }
    //Get artist info for the dance page
    public function getDanceArtist(){
      $this->query(
        "SELECT`Artist`.`ArtistId`,
        `Artist`.`Name`,
        `Artist`.`Description`
        FROM Artist
        WHERE `EventId` = 4"
        );
        $result = $this->resultSet();
        return $result;
    }
}
