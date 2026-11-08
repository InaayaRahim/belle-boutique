<?php

$host = "localhost";
$port = 3306;
$dbname = "belle_boutique";
$username = "root";
$password = "root";

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>