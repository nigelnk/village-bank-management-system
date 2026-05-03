<?php

require_once './utils/config.php';

$mysqli = get_server_db();
$dbname = "village_bank";

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// check if the database exists
$db_exists = $mysqli->query("SHOW DATABASES LIKE '$dbname'");

if ($db_exists && $db_exists->num_rows > 0) {
    // database exists, redirect to login page
    header("Location: ./auth/login.php");
    exit();
} else {
    // if database does not exist, run setup
    require_once 'utils/db_setup.php';
}

$mysqli->close();
