<?php
/**
 * Database Connection Diagnostic Tool
 */
require_once 'includes/config.php';
require_once 'includes/db.php';

echo "<h2>Database Connection Status</h2>";

try {
    $conn = $db->getConnection();
    echo "<p style='color: green;'>✅ Connected to database: <b>" . DB_NAME . "</b></p>";
    
    // Check for users table
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ 'users' table exists.</p>";
        
        // Count users
        $res = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $res->fetch_assoc()['count'];
        echo "<p>Total users in database: <b>$count</b></p>";
        
        if ($count == 0) {
            echo "<p style='color: orange;'>⚠️ No users found. You need to Register first!</p>";
        } else {
             $res = $conn->query("SELECT id, name, email FROM users LIMIT 1");
             $user = $res->fetch_assoc();
             echo "<p>User sample found: <b>" . $user['name'] . " (" . $user['email'] . ")</b></p>";
        }
    } else {
        echo "<p style='color: red;'>❌ 'users' table is missing! Please run <a href='setup.php'>setup.php</a>.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Did you start MySQL in the XAMPP Control Panel?</p>";
}
?>
