<?php
require_once 'config.php';
require_once 'functions.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// ========================================
// REGISTER
// ========================================
if ($action === 'register') {
    
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $height = intval($_POST['height'] ?? 0);
    $age = intval($_POST['age'] ?? 0);
    $gender = sanitizeInput($_POST['gender'] ?? 'other');
    
    if (empty($email) || empty($password) || empty($full_name)) {
        sendResponse(false, 'All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, 'Invalid email address');
    }
    
    if (strlen($password) < 6) {
        sendResponse(false, 'Password must be at least 6 characters');
    }
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        sendResponse(false, 'Email already registered');
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, full_name, height, age, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $email, $password_hash, $full_name, $height, $age, $gender);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        
        // Create user stats
        $stmt2 = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (?)");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        
        sendResponse(true, 'Registration successful', ['user_id' => $user_id]);
    } else {
        sendResponse(false, 'Registration failed');
    }
}

// ========================================
// LOGIN
// ========================================
else if ($action === 'login') {
    
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        sendResponse(false, 'Email and password are required');
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT user_id, email, password_hash, full_name FROM users WHERE email = ? AND is_active = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(false, 'Invalid email or password');
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        sendResponse(false, 'Invalid email or password');
    }
    
    // Set session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    
    sendResponse(true, 'Login successful', [
        'user_id' => $user['user_id'],
        'email' => $user['email'],
        'full_name' => $user['full_name']
    ]);
}

// ========================================
// LOGOUT
// ========================================
else if ($action === 'logout') {
    session_destroy();
    sendResponse(true, 'Logged out successfully');
}

// ========================================
// CHECK SESSION
// ========================================
else if ($action === 'check') {
    if (isset($_SESSION['user_id'])) {
        sendResponse(true, 'Logged in', [
            'user_id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'full_name' => $_SESSION['full_name']
        ]);
    } else {
        sendResponse(false, 'Not logged in');
    }
}

else {
    sendResponse(false, 'Invalid action');
}
?>