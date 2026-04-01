<?php
/**
 * Ultimate Database Setup Helper
 */
require_once 'includes/config.php';

echo "<h2>GymPlanner Pro Setup</h2>";

// 1. Connect to MySQL (without selecting a DB yet)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("<p style='color:red'>❌ Connection failed: " . $conn->connect_error . ". Check if XAMPP MySQL is running!</p>");
}

// 2. Create Database
if ($conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME)) {
    echo "<p style='color:green'>✅ Database '" . DB_NAME . "' is ready.</p>";
} else {
    die("<p style='color:red'>❌ Error creating database: " . $conn->error . "</p>");
}

$conn->select_db(DB_NAME);

// 3. Run Schema
$schema = file_get_contents('db/schema.sql');
// Remove comments and split by semicolon
$schema = preg_replace('/--.*$/m', '', $schema); 
$statements = array_filter(array_map('trim', explode(';', $schema)));

$successCount = 0;
foreach ($statements as $index => $statement) {
    if (!empty($statement)) {
        if ($conn->query($statement)) {
            $successCount++;
        } else {
            // Check if it's just a duplicate entry error (normal if running setup again)
            if ($conn->errno !== 1062) {
                echo "<p style='color:orange'>⚠️ Warning in statement #".($index+1).": " . $conn->error . "</p>";
            }
        }
    }
}

echo "<p style='color:green'>✅ Setup completed! Executed $successCount SQL statements.</p>";
echo "<p><b>Next Step:</b> <a href='/workout-planner/'>Go to Home Page and Register!</a></p>";

$conn->close();
?>
