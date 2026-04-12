<?php
// ============================================
// MIGRATION: Add role column to users table
// Run once, then DELETE this file
// ============================================
require_once 'config/db.php';

try {
    // Add role column if it doesn't exist
    $pdo->exec("
        ALTER TABLE users
        ADD COLUMN IF NOT EXISTS role ENUM('admin','user') NOT NULL DEFAULT 'user'
    ");

    // Make sure the original admin account has admin role
    $pdo->exec("UPDATE users SET role = 'admin' WHERE username = 'admin'");

    echo "<b style='color:green'>✅ Migration complete!</b><br>";
    echo "- Added 'role' column to users table<br>";
    echo "- Set admin user role to 'admin'<br><br>";

    // Show current users
    $stmt = $pdo->query("SELECT id, username, role FROM users");
    $users = $stmt->fetchAll();
    echo "<table border='1' cellpadding='6'><tr><th>ID</th><th>Username</th><th>Role</th></tr>";
    foreach ($users as $u) {
        echo "<tr><td>{$u['id']}</td><td>{$u['username']}</td><td>{$u['role']}</td></tr>";
    }
    echo "</table><br>";
    echo "<b style='color:red'>⚠️ DELETE this file (migrate_roles.php) now!</b>";
} catch (PDOException $e) {
    echo "<b style='color:red'>❌ Migration failed: " . htmlspecialchars($e->getMessage()) . "</b>";
}
