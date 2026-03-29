<?php
require 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query('SELECT * FROM workouts ORDER BY created_at DESC');
    $workouts = $stmt->fetchAll();
    foreach ($workouts as &$workout) {
        $exStmt = $pdo->prepare('SELECT * FROM exercises WHERE workout_id = ?');
        $exStmt->execute([$workout['id']]);
        $workout['exercises'] = $exStmt->fetchAll();
    }
    echo json_encode($workouts);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
