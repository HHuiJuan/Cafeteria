<?php
define('DB_HOST', '52.203.57.254');
define('DB_NAME', 'cafeteria');
define('DB_USER', 'cafeadmin');
define('DB_PASS', 'Cafe-pass1234');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
