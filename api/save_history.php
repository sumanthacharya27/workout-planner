<?php
// ============================================
// SAVE WORKOUT HISTORY API
// ============================================
// api/save_history.php
// Records completed workout in history

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';
requireAuth();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['workoutId']) || empty($data['duration'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Workout ID and duration are required']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Insert workout history
    $stmt = $pdo->prepare("
        INSERT INTO workout_history (user_id, workout_id, duration, notes)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        (int)$data['workoutId'],
        (int)$data['duration'],
        sanitize($data['notes'] ?? '')
    ]);
    
    // Update user stats
    $statsStmt = $pdo->prepare("
        SELECT id, total_workouts, total_exercises, total_time FROM user_stats WHERE user_id = ? LIMIT 1
    ");
    $statsStmt->execute([$userId]);
    $stats = $statsStmt->fetch();
    
    // Get exercise count for this workout
    $exerciseStmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM exercises WHERE workout_id = ?
    ");
    $exerciseStmt->execute([(int)$data['workoutId']]);
    $exerciseData = $exerciseStmt->fetch();
    $exerciseCount = $exerciseData['count'];
    
    // Update stats
    $updateStmt = $pdo->prepare("
        UPDATE user_stats SET 
            total_workouts = total_workouts + 1,
            total_exercises = total_exercises + ?,
            total_time = total_time + ?,
            last_workout_date = CURDATE()
        WHERE user_id = ?
    ");
    $updateStmt->execute([
        $exerciseCount,
        (int)$data['duration'],
        $userId
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Workout history saved successfully'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save workout history'
    ]);
}
