<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$port = 3306;
$dbname = "belle_boutique";
$username = "root";
$password = "root";

try {
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage());

    die("Sorry, we're unable to connect to the database right now.");
}

?>