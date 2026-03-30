<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (!$auth->isLoggedIn()) {
    error("Not authenticated", 401);
}

validateRequest('POST');
$input = getJSONInput();

$action = $input['action'] ?? null;
$user_id = $auth->getCurrentUserId();

if ($action === 'create') {
    if (!isset($input['name'], $input['exercises'])) {
        error("Missing required fields", 400);
    }
    
    $name = $db->escape($input['name']);
    $description = $db->escape($input['description'] ?? '');
    
    $sql = "INSERT INTO custom_workouts (user_id, name, description) VALUES ($user_id, '$name', '$description')";
    if ($db->query($sql)) {
        $workout_id = $db->lastInsertId();
        
        // Add exercises to workout
        foreach ($input['exercises'] as $ex) {
            $exercise_id = (int)$ex['exercise_id'];
            $sets = (int)($ex['sets'] ?? 3);
            $reps = (int)($ex['reps'] ?? 10);
            $rest = (int)($ex['rest_seconds'] ?? 60);
            
            $sql = "INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps, rest_seconds) 
                    VALUES ($workout_id, $exercise_id, $sets, $reps, $rest)";
            $db->query($sql);
        }
        
        success("Workout created", ['workout_id' => $workout_id]);
    } else {
        error("Failed to create workout", 500);
    }
}
elseif ($action === 'get') {
    $result = $db->query("SELECT * FROM custom_workouts WHERE user_id = $user_id ORDER BY created_at DESC");
    $workouts = [];
    
    while ($row = $result->fetch_assoc()) {
        $workouts[] = $row;
    }
    
    success("Workouts fetched", ['workouts' => $workouts]);
}
elseif ($action === 'get_detail') {
    $workout_id = (int)($input['workout_id'] ?? 0);
    
    $result = $db->query("SELECT * FROM custom_workouts WHERE id = $workout_id AND user_id = $user_id");
    if ($result->num_rows === 0) {
        error("Workout not found", 404);
    }
    
    $workout = $result->fetch_assoc();
    
    $exercises_result = $db->query("
        SELECT we.*, e.name, e.description, e.muscle_group, e.difficulty 
        FROM workout_exercises we 
        JOIN exercises e ON we.exercise_id = e.id 
        WHERE we.workout_id = $workout_id
    ");
    
    $exercises = [];
    while ($row = $exercises_result->fetch_assoc()) {
        $exercises[] = $row;
    }
    
    $workout['exercises'] = $exercises;
    success("Workout details fetched", ['workout' => $workout]);
}
else {
    error("Unknown action", 400);
}
