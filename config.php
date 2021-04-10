<?php

// Database parameters
define('DB_HOST', 'rdbms.strato.de'); 
// Add your DB root
define('DB_USER', 'dbu469977'); 
//Add your DB password
define('DB_PASS', 'nvm03ntmf#&#FAQ%#$#JE'); 
//Add your DB Name
define('DB_NAME', 'dbs1716329'); 
//APPROOT
define('APPROOT', dirname(dirname(__FILE__)));
//URLROOT (Dynamic links)
define('URLROOT', 'https://cydesign.nl/noted');

// sitename
define('SITENAME', 'Haarlem Festival');


// constants for validations
define('NUMBERVALIDATION' , "/^[0-9.]*$/");

define('NAMEVALIDATION' , '/^[a-zA-Z0-9_ ]*$/');

define('PASSWORDVALIDATION' , '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}');

define("EMAILCHECKSALT", "rb2GkYCioQTEaeWLpHajZZf4ZRzLSHVT");

define("MAILPASSWORD", "8uknTnno9CpXia9WrtZ12ihg5a4El11E");

DEFINE("PAYMENTKEY", "test_xqPuQB9Sz4GerN9vpEBA5JFh4SAjAR");