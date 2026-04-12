<?php
// ============================================
// GET USER STATS API
// ============================================
// api/get_stats.php

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/db.php';

requireAuth();

$userId = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT total_workouts, total_exercises, total_time, current_streak, last_workout_date
        FROM user_stats
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();

    if (!$stats) {
        $stats = [
            'total_workouts'    => 0,
            'total_exercises'   => 0,
            'total_time'        => 0,
            'current_streak'    => 0,
            'last_workout_date' => null,
        ];
    }

    echo json_encode(['success' => true, 'data' => $stats]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch stats']);
}
