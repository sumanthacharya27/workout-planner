<?php
require 'db.php';
header('Content-Type: application/json');

// For demo: return all workouts as history (customize as needed)
try {
    $stmt = $pdo->query('SELECT * FROM workouts ORDER BY created_at DESC');
    $history = $stmt->fetchAll();
    echo json_encode($history);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
