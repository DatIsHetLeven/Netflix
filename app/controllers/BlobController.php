<?php
require_once(APPROOT . "/helper/UtilHelper.php");

class BlobController extends Autoloader {
    private $MAX_FILE_SIZE_UPLOAD_LIMIT = 8388608; // In bytes
    private $blobDir = APPROOT . "/blob/";

    public function storeFile($file, $acceptedMimeTypes = array(), $overrideFileName = null) {
        $token = $overrideFileName !== null ? $overrideFileName : UtilHelper::randomString(64);
        $location = $this->getBlobLocation($token);

        if (filesize($file["tmp_name"]) > $this->MAX_FILE_SIZE_UPLOAD_LIMIT) return "fileExceedsMaxUploadSize";
        if (count($acceptedMimeTypes) > 0 && !in_array($file["type"], $acceptedMimeTypes)) return "rejected";

        move_uploaded_file($file["tmp_name"], $location);
        return $token;
    }

    public function image() {
        if (!isset($_GET["token"])) {
            http_response_code(400);
            exit();
        } else {
            $this->getFile($_GET["token"], ACCEPTEDIMAGETYPES);
        }
    }

    private function getFile($token, $filterMineTypes = null) {
        if ($filterMineTypes === null) $filterMineTypes = array();

        // Clean token to make sure the request cannot leak into other directories
        $token = str_replace(array("/", "\\"), "", $token);
        $location = $this->getBlobLocation($token);

        // Check if the file exists
        if (!file_exists($location)) {
            http_response_code(404);
            exit();
        }

        // Gets the mime type of the file and checks if it should be send
        $mimeType = mime_content_type($location);
        $mineTypeInFilter = count($filterMineTypes) > 0 ? in_array($mimeType, $filterMineTypes) : true;

        if (!file_exists($location) || !$mineTypeInFilter) {
            http_response_code(404);
        } else {
            $contentLength = filesize($location);

            http_response_code(200);
            header("Cache-Control: public");
            header("Content-Length: $contentLength");
            header("Content-Type: $mimeType");
            echo file_get_contents($location);
        }
    }

    public function deleteFile($token) {
        $blobLocation = $this->getBlobLocation($token);
        if (!file_exists($blobLocation)) return "fileDoesNotExist";

        unlink($blobLocation);
        return "success";
    }

    public function getBlobLocation($token) {
        return $this->blobDir . $token;
    }

    public static function getImageUrl($token) {
        return URLROOT . "/blob/image?token=" . $token;
    }
}