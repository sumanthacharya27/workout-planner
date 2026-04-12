<?php
// ============================================
// USER REGISTRATION API
// ============================================
// api/register.php
// Registers a new user account

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username is required']);
        exit;
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username must be between 3 and 50 characters']);
        exit;
    }

    if (empty($password) || strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
        exit;
    }

    // Check if username already exists
    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $checkStmt->execute([sanitize($username)]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Username already taken. Please choose another.']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $insertStmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $insertStmt->execute([sanitize($username), $hashedPassword]);
    $newUserId = $pdo->lastInsertId();

    // Auto-login: set session
    $_SESSION['user_id'] = $newUserId;
    $_SESSION['username'] = sanitize($username);
    $_SESSION['role'] = 'user'; // New registrations are always regular users

    // Initialize user stats for the new user
    $statsStmt = $pdo->prepare('INSERT INTO user_stats (user_id) VALUES (?)');
    $statsStmt->execute([$newUserId]);

    echo json_encode([
        'success'  => true,
        'message'  => 'Account created successfully',
        'userId'   => $newUserId,
        'username' => sanitize($username)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Registration failed. Please try again.'
    ]);
}
