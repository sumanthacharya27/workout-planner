<?php
// ============================================
// DELETE WORKOUT API
// ============================================
// api/delete_workout.php
// Admin can delete any workout; users can only delete their own custom workouts

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
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['workoutId'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Workout ID is required']);
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

    // Non-admins can only delete their own custom workouts
    if (!isAdmin()) {
        if (!$workout['is_custom'] || (int)$workout['created_by'] !== (int)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You can only delete your own custom workouts']);
            exit;
        }
    }

    // Delete workout (cascading delete handles exercises)
    $stmt = $pdo->prepare("DELETE FROM workouts WHERE id = ?");
    $stmt->execute([$workoutId]);

    echo json_encode([
        'success' => true,
        'message' => 'Workout deleted successfully'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to delete workout'
    ]);
}

