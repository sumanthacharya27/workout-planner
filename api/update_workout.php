<?php
// ============================================
// UPDATE WORKOUT API
// ============================================
// api/update_workout.php
// Admin can update any workout; users can only update their own custom workouts

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

// Must be authenticated
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

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
    if (empty($data['workoutId']) || empty($data['name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Workout ID and name are required']);
        exit;
    }

    $workoutId = (int)$data['workoutId'];

    // Check the workout exists and get ownership info
    $check = $pdo->prepare("SELECT id, is_custom, created_by FROM workouts WHERE id = ?");
    $check->execute([$workoutId]);
    $workout = $check->fetch();

    if (!$workout) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Workout not found']);
        exit;
    }

    // Non-admins can only edit their own custom workouts
    if (!isAdmin()) {
        if (!$workout['is_custom'] || (int)$workout['created_by'] !== (int)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You can only edit your own custom workouts']);
            exit;
        }
    }

    $pdo->beginTransaction();

    // Update workout
    $stmt = $pdo->prepare("
        UPDATE workouts 
        SET name = ?, description = ?, difficulty = ?
        WHERE id = ?
    ");
    $stmt->execute([
        sanitize($data['name']),
        sanitize($data['description'] ?? ''),
        sanitize($data['difficulty'] ?? 'beginner'),
        $workoutId
    ]);

    // Replace exercises (delete old, insert new)
    $pdo->prepare("DELETE FROM exercises WHERE workout_id = ?")->execute([$workoutId]);

    if (!empty($data['exercises'])) {
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
    }

    $pdo->commit();

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Workout updated successfully'
    ]);
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to update workout'
    ]);
}
