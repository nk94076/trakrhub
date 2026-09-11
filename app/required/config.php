<?php
date_default_timezone_set('Asia/Kolkata'); // India ke liye
$host = getenv('DB_HOST') ?: "localhost";
$dbname = getenv('DB_NAME') ?: "u340685552_trakrhub";
$username = getenv('DB_USER') ?: "u340685552_trakrhub";
$password = getenv('DB_PASSWORD') ?: "";

// Database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
