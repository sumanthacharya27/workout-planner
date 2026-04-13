<?php
// ============================================
// DATABASE CONNECTION FILE
// ============================================
// config/db.php
// Change these credentials to match your XAMPP setup

define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Default XAMPP username
define('DB_PASS', '');              // Default XAMPP password (empty)
define('DB_NAME', 'workout_planner');

try {
    // Create PDO connection
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Send JSON response
 */
function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && strtolower(trim($_SESSION['role'])) === 'admin';
}

/**
 * Require admin authentication — sends 403 if not admin
 */
function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isAuthenticated()) {
        sendJSON(['error' => 'Unauthorized'], 401);
    }
    if (!isAdmin()) {
        sendJSON(['error' => 'Forbidden: Admin access required'], 403);
    }
}

/**
 * Verify admin authentication
 */
function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isAuthenticated()) {
        sendJSON(['error' => 'Unauthorized'], 401);
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Sanitize string input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate input data
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    return $errors;
}
