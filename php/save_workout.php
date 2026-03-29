<?php
require 'db.php';

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['name']) || !isset($data['exercises'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$name = $data['name'];
$description = isset($data['description']) ? $data['description'] : '';
$exercises = $data['exercises'];

try {
    // Insert workout
    $stmt = $pdo->prepare('INSERT INTO workouts (name, description) VALUES (?, ?)');
    $stmt->execute([$name, $description]);
    $workout_id = $pdo->lastInsertId();

    // Insert exercises
    $stmt = $pdo->prepare('INSERT INTO exercises (workout_id, name, sets, reps, weight, rest) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($exercises as $ex) {
        $stmt->execute([
            $workout_id,
            $ex['name'],
            $ex['sets'],
            $ex['reps'],
            $ex['weight'],
            $ex['rest']
        ]);
    }
    echo json_encode(['success' => true, 'workout_id' => $workout_id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
