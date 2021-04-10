<?php
require_once APPROOT."/helper/PasswordHelper.php";
require_once APPROOT."/helper/MailHelper.php";
require_once APPROOT."/helper/EmployeeSessionHelper.php";
require_once APPROOT."/controllers/BlobController.php";

class CmsController extends Autoloader {
    private $data = [
        "status" => "unset",
        "employees" => [],
    ];

    public function index() {
        header("Location: " . URLROOT . "/cms/auth/login");
    }

    public function dashboard() {
        EmployeeSessionHelper::guardPage("volunteer");

        $eventsModel = $this->model("Event");
        $this->data["events"] = $eventsModel->getAllEvents();

        $this->view("cms/dashboard", $this->data);
    }
}