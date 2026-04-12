<?php
// ============================================
// SAVE WORKOUT HISTORY API
// ============================================
// api/save_history.php

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/db.php';

requireAuth();

try {
    $data     = json_decode(file_get_contents('php://input'), true);
    $userId   = (int)$_SESSION['user_id'];

    if (empty($data['workoutId']) || !isset($data['duration'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Workout ID and duration are required']);
        exit;
    }

    $workoutId = (int)$data['workoutId'];
    $duration  = (int)$data['duration'];
    $notes     = sanitize($data['notes'] ?? '');

    // Insert history row — includes user_id
    $stmt = $pdo->prepare("
        INSERT INTO workout_history (workout_id, user_id, duration, notes)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$workoutId, $userId, $duration, $notes]);

    // Count exercises for this workout
    $exStmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM exercises WHERE workout_id = ?");
    $exStmt->execute([$workoutId]);
    $exCount = (int)($exStmt->fetch()['cnt'] ?? 0);

    // Upsert per-user stats row with streak logic
    $upsert = $pdo->prepare("
        INSERT INTO user_stats (user_id, total_workouts, total_exercises, total_time, current_streak, last_workout_date)
        VALUES (?, 1, ?, ?, 1, CURDATE())
        ON DUPLICATE KEY UPDATE
            total_workouts  = total_workouts  + 1,
            total_exercises = total_exercises + VALUES(total_exercises),
            total_time      = total_time      + VALUES(total_time),
            current_streak  = IF(
                last_workout_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY),
                current_streak + 1,
                IF(last_workout_date = CURDATE(), current_streak, 1)
            ),
            last_workout_date = CURDATE()
    ");
    $upsert->execute([$userId, $exCount, $duration]);

    echo json_encode(['success' => true, 'message' => 'Workout history saved successfully']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save workout history: ' . $e->getMessage()]);
}
