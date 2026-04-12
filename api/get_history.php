<?php
// ============================================
// GET WORKOUT HISTORY API
// ============================================
// api/get_history.php
// Returns workout history records

header('Content-Type: application/json');
require_once '../config/db.php';
requireAuth();

try {
    $filter = $_GET['filter'] ?? 'all';
    $dateCondition = '';
    
    if ($filter === 'week') {
        $dateCondition = "AND wh.completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($filter === 'month') {
        $dateCondition = "AND wh.completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }
    
    $query = "
        SELECT 
            wh.id,
            wh.workout_id,
            wh.duration,
            wh.completed_at,
            w.name as workout_name,
            (SELECT COUNT(*) FROM exercises WHERE workout_id = wh.workout_id) as exercise_count
        FROM workout_history wh
        JOIN workouts w ON wh.workout_id = w.id
        WHERE wh.user_id = ? $dateCondition
        ORDER BY wh.completed_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $history
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch history'
    ]);
}
