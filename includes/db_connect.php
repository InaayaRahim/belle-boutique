<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Load environment configuration
$envPath = dirname(__DIR__) . '/.env';

if (!file_exists($envPath)) {
    die("Database configuration file is missing.");
}

$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);

if ($env === false) {
    die("Unable to load database configuration.");
}

// Database settings
$host = $env['DB_HOST'];
$port = (int) $env['DB_PORT'];
$dbname = $env['DB_NAME'];
$username = $env['DB_USER'];
$password = $env['DB_PASS'];

try {
    $conn = new mysqli(
        $host,
        $username,
        $password,
        $dbname,
        $port
    );

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Sorry, we're unable to connect to the database right now.");
}

?>