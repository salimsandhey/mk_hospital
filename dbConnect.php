<?php

require 'vendor/autoload.php'; // Load Composer's autoloader

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_NAME'];
$db_user = $_ENV['DB_USER'];
$db_password = $_ENV['DB_PASSWORD'];


// Create a connection to the central database
$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


