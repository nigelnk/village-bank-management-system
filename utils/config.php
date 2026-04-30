<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'chindevu');
define('DB_PASS', ''); // replace the password with your db password or pakhale empty if there is no password
define('DB_NAME', 'root');

// will connect to MySQL server only first, since this is the initial setup, we dont have our actual village_bank database
function get_server_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

    if ($conn->connect_error) {
        die("Server connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// then connect to village_bank database
function get_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
