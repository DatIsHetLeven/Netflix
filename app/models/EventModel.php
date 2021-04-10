<?php
require_once APPROOT . "/classes/Event.php";

class EventModel extends Database {
    public function getById($id) {
        $this->query(
            "SELECT *
            FROM `Event`
            WHERE `EventId` = :id"
        );

        $this->bind(":id", $id);

        return $this->processSingle(
            $this->single()
        );
    }

    public function getAllEvents() {
        $this->query(
            "SELECT *
            FROM `Event`"
        );

        return $this->processMultiple(
            $this->resultSet()
        );
    }

    public function updateEvent($id, $name, $shortName, $description, $imageId) {
        $this->query(
            "UPDATE `Event`
            SET `Name` = :name,
                `ShortName` = :shortName,
                `Description` = :description,
                `ImageId` = :imageId
            WHERE `EventId` = :id"
        );

        $this->bind(":id", $id);
        $this->bind(":name", $name);
        $this->bind(":shortName", $shortName);
        $this->bind(":description", $description);
        $this->bind(":imageId", $imageId);

        $this->execute();
    }

    private function processMultiple($set) {
        return array_map(function ($row) { return $this->processSingle($row); }, $set);
    }

    private function processSingle($item) {
        return new Event(
            $item->EventId,
            $item->Name,
            $item->ShortName,
            $item->Description,
            $item->ImageId
        );
    }

    //Alle info van de events voor de index page.
    public function getIndexEventInfo(){
      $this->query("SELECT `Event`.`EventId`,`Event`.`Name`,`Event`.`ShortName`,`Event`.`Description`,`Event`.`ImageId`
      FROM Event"
    );
    $result = $this->resultSet();
    return $result;
    }
}
