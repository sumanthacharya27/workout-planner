<?php
// ============================================
// SAVE CUSTOM WORKOUT API
// ============================================
// api/save_workout.php
// Creates a new custom workout

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['name']) || empty($data['exercises'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and exercises are required']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert workout
    $stmt = $pdo->prepare("
        INSERT INTO workouts (name, description, is_custom, created_by)
        VALUES (?, ?, 1, ?)
    ");
    $stmt->execute([
        sanitize($data['name']),
        sanitize($data['description'] ?? ''),
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
    
    echo json_encode([
        'success' => true,
        'message' => 'Workout saved successfully',
        'workoutId' => $workoutId
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save workout'
    ]);
}
