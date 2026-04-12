<?php
// ============================================
// GET WORKOUTS API
// ============================================
// api/get_workouts.php
// Returns all pre-made and custom workouts with exercises

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/db.php';

requireAuth(); // must be logged in

try {
    // Get all workouts with their exercises
    $stmt = $pdo->query("
        SELECT 
            w.id,
            w.name,
            w.description,
            w.difficulty,
            w.is_custom,
            w.created_by,
            w.created_at
        FROM workouts w
        ORDER BY w.is_custom ASC, w.created_at DESC
    ");
    
    $workouts = $stmt->fetchAll();
    
    // Get exercises for each workout
    foreach ($workouts as &$workout) {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                name,
                sets,
                reps,
                weight,
                rest_time,
                notes,
                exercise_order
            FROM exercises
            WHERE workout_id = ?
            ORDER BY exercise_order ASC
        ");
        $stmt->execute([$workout['id']]);
        $workout['exercises'] = $stmt->fetchAll();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $workouts
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch workouts'
    ]);
}
