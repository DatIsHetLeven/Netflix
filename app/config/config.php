<?php

// Database parameters
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'Festival');

// Application root
define('APPROOT',dirname((__DIR__)));

define('ROOT',dirname(dirname(dirname(__FILE__))));

//urlroot (dynamic links)
define('URLROOT', 'http://localhost/haarlemfestival');

// sitename
define('SITENAME', 'Haarlem Festival');


// constants for validations
define('NUMBERVALIDATION' , "/^[0-9.]*$/");

define('NAMEVALIDATION' , '/^[a-zA-Z0-9_ ]*$/');

define('PASSWORDVALIDATION' , '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}');

define("EMAILCHECKSALT", "rb2GkYCioQTEaeWLpHajZZf4ZRzLSHVT");

define("MAILPASSWORD", "8uknTnno9CpXia9WrtZ12ihg5a4El11E");

define("ACCEPTEDIMAGETYPES", ["image/png", "image/jpeg", "image/jpg"]);

// define("PAYMENTKEY", "test_Ds3fz4U9vNKxzCfVvVHJT2sgW5ECD8"); // thijs
define("PAYMENTKEY", "test_xqPuQB9Sz4GerN9vpEBA5JFh4SAjAR");


// define('REMOTEURLROOT', 'https://59f2bb621008.ngrok.io/haarlemfestival'); // test ngrok mollie tunnuling
