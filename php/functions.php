<?php
// Helper functions

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function ensureUser($conn, $user_id) {
    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Create default user
        $stmt2 = $conn->prepare("INSERT INTO users (user_id, email, password_hash, full_name) VALUES (?, 'default@gym.com', 'N/A', 'Gym User')");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        
        // Create user stats
        $stmt3 = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (?)");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
    }
}
?>