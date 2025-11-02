<?php
$host = "localhost";
$user = "root";   // default user in XAMPP
$pass = "";       // default password is empty
$dbname = "hardware_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
