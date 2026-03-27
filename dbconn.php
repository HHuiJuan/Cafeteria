<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "cafe-db.chhcwyw7nw2u.us-east-1.rds.amazonaws.com";
$user = "cafeadmin";
$pass = "Cafe-pass1234";   // replace with your actual password
$db   = "cafeteria";

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    echo "Connected successfully to RDS!";
    return $conn;
}
echo "✅ Connected successfully to RDS!";
?>
