<?php
require_once APPROOT."/classes/Page.php";

class PageModel extends Database {
    public function getAllPages() {
        $this->query(
            "SELECT *
            FROM `InfoPages`;"
        );

        return array_map(
            function ($row) {
                return new Page(
                    intval($row->PageId),
                    $row->PageTitle,
                    $row->Content
                );
            },
            $this->resultSet()
        );
    }

    public function getPageById($pageId) {
        $this->query(
            "SELECT *
            FROM `InfoPages`
            WHERE `PageId` = :pageId"
        );

        $this->bind(":pageId", $pageId);
        $row = $this->single();

        return new Page(
            intval($row->PageId),
            $row->PageTitle,
            $row->Content
        );
    }

    public function createPage($title, $jsonContent) {
        $this->query(
            "INSERT INTO `InfoPages`
            VALUES (null, :title, :jsonContent);"
        );

        $this->bind(":title", $title);
        $this->bind(":jsonContent", $jsonContent);

        $this->execute();
    }

    public function updatePage($pageId, $title, $jsonContent) {
        $this->query(
            "UPDATE `InfoPages`
            SET `PageTitle` = :title,
                `Content` = :jsonContent
            WHERE `PageId` = :pageId;"
        );

        $this->bind(":pageId", $pageId);
        $this->bind(":title", $title);
        $this->bind(":jsonContent", $jsonContent);

        $this->execute();
    }

    public function deletePage($pageId) {
        $this->query(
            "DELETE FROM `InfoPages`
            WHERE `PageId` = :pageId;"
        );

        $this->bind(":pageId", $pageId);

        $this->execute();
    }
}