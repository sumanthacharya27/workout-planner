<?php
/**
 * Database Setup Helper
 * Run this once to initialize the database
 */

require_once 'includes/config.php';

// Read schema
$schema = file_get_contents('db/schema.sql');

// Create database first
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$conn->select_db(DB_NAME);

// Split and execute statements
$statements = array_filter(array_map('trim', explode(';', $schema)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        if (!$conn->query($statement)) {
            echo "Error: " . $conn->error . "<br>";
        }
    }
}

$conn->close();

echo "Database setup completed successfully!";
?>
