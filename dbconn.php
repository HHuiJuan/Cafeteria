<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('DB_HOST', 'cafe-db.chhcwyw7nw2u.us-east-1.rds.amazonaws.com');
define('DB_NAME', 'cafeteria');
define('DB_USER', 'cafeadmin');
define('DB_PASS', 'Cafe-pass1234');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
