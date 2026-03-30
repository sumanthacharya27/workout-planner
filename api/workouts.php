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
elseif ($action === 'get_history') {
    $result = $db->query("SELECT wh.*, cw.name AS workout_name FROM workout_history wh JOIN custom_workouts cw ON wh.workout_id=cw.id WHERE wh.user_id = $user_id ORDER BY completed_at DESC");
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    success("History fetched", ['history' => $history]);
}
elseif ($action === 'complete') {
    $workout_id = (int)($input['workout_id'] ?? 0);
    $duration = (int)($input['duration_minutes'] ?? 0);
    $notes = $db->escape($input['notes'] ?? '');
    $execution_data = $input['execution_data'] ?? [];

    // validate workout ownership
    $check = $db->query("SELECT id FROM custom_workouts WHERE id = $workout_id AND user_id = $user_id");
    if ($check->num_rows === 0) {
        error("Workout not found", 404);
    }

    $sql = "INSERT INTO workout_history (user_id, workout_id, completed_at, duration_minutes, notes) VALUES ($user_id, $workout_id, NOW(), $duration, '$notes')";
    if ($db->query($sql)) {
        $history_id = $db->lastInsertId();
        
        // Save detailed execution data if provided
        if (!empty($execution_data)) {
            foreach ($execution_data as $exercise_index => $sets) {
                foreach ($sets as $set_index => $set_data) {
                    if ($set_data['completed']) {
                        $reps = (int)($set_data['reps'] ?? 0);
                        $set_notes = $db->escape($set_data['notes'] ?? '');
                        
                        // You could add a workout_execution_details table here for detailed tracking
                        // For now, we'll just log the completion
                    }
                }
            }
        }
        
        success("Workout completed and logged", ['history_id' => $history_id]);
    } else {
        error("Failed to log workout history", 500);
    }
}
else {
    error("Unknown action", 400);
}
