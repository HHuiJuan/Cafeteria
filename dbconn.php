<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "cafe-db.chhcwyw7nw2u.us-east-1.rds.amazonaws.com";
$user = "cafeadmin";
$pass = "Cafe-pass1234";   // replace with your actual password
$db   = "cafeteria";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "✅ Connected successfully to RDS!";
?>
