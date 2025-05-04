<?php
// Database connection using environment variables
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'travel_db';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("MySQL Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");
?>