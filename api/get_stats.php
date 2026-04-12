<?php
// ============================================
// GET USER STATS API
// ============================================
// api/get_stats.php
// Returns user statistics

header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT 
            total_workouts,
            total_exercises,
            total_time,
            current_streak,
            last_workout_date
        FROM user_stats
        LIMIT 1
    ");
    
    $stats = $stmt->fetch();
    
    // Calculate streak if not set
    if (!$stats) {
        $stats = [
            'total_workouts' => 0,
            'total_exercises' => 0,
            'total_time' => 0,
            'current_streak' => 0,
            'last_workout_date' => null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch stats'
    ]);
}
