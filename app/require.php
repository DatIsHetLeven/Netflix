<?php
// De libraries folder opvragen
require_once 'libraries/Url.php';
require_once 'libraries/Autoloader.php';
require_once 'libraries/Database.php';
require_once 'config/config.php';

require_once APPROOT . "/classes/Customer.php";
require_once APPROOT . "/classes/Employee.php";

if(session_id() == ''){
    session_start();

    if (!isset($_SESSION["loggedInCustomer"])) $_SESSION["loggedInCustomer"] = null;
    if (!isset($_SESSION["loggedInEmployee"])) $_SESSION["loggedInEmployee"] = null;
}

// een url object maken
$url = new Url();
