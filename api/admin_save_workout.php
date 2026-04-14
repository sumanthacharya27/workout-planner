<?php
// ============================================
// ADMIN: SAVE PRE-MADE WORKOUT API
// ============================================
// api/admin_save_workout.php
// Admin creates pre-made workouts

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

// Admin-only: check authentication and role
requireAdmin();

try {
    ob_start();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON payload']);
        exit;
    }

    // CSRF validation
    $clientToken = $data['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (empty($sessionToken) || !hash_equals($sessionToken, $clientToken)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid or missing CSRF token']);
        exit;
    }
    
    // Validate input
    if (empty($data['name']) || empty($data['exercises'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and exercises are required']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert workout as pre-made (is_custom = 0)
    $stmt = $pdo->prepare("
        INSERT INTO workouts (name, description, difficulty, is_custom, created_by)
        VALUES (?, ?, ?, 0, ?)
    ");
    $stmt->execute([
        sanitize($data['name']),
        sanitize($data['description'] ?? ''),
        sanitize($data['difficulty'] ?? 'beginner'),
        $_SESSION['user_id']
    ]);
    
    $workoutId = $pdo->lastInsertId();
    
    // Insert exercises
    $exerciseStmt = $pdo->prepare("
        INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, notes, exercise_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($data['exercises'] as $index => $exercise) {
        $exerciseStmt->execute([
            $workoutId,
            sanitize($exercise['name']),
            (int)$exercise['sets'],
            (int)$exercise['reps'],
            (float)($exercise['weight'] ?? 0),
            (int)($exercise['rest'] ?? 60),
            sanitize($exercise['notes'] ?? ''),
            $index + 1
        ]);
    }
    
    $pdo->commit();
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Pre-made workout created successfully',
        'workoutId' => $workoutId
    ]);
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to create workout'
    ]);
}
