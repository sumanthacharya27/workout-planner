<?php
require_once 'config.php';
require_once 'functions.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    sendResponse(false, 'Please login first');
}

$action = $_GET['action'] ?? '';
$user_id = getCurrentUserId();

// ========================================
// CREATE WORKOUT
// ========================================
if ($action === 'create') {
    
    $plan_name = sanitizeInput($_POST['plan_name'] ?? '');
    $difficulty = sanitizeInput($_POST['difficulty'] ?? 'beginner');
    $description = sanitizeInput($_POST['description'] ?? '');
    $exercises = json_decode($_POST['exercises'] ?? '[]', true);
    
    // Validation
    if (empty($plan_name)) {
        sendResponse(false, 'Workout name is required');
    }
    
    if (empty($exercises) || count($exercises) === 0) {
        sendResponse(false, 'At least one exercise is required');
    }
    
    // Insert workout plan
    $stmt = $conn->prepare("INSERT INTO workout_plans (user_id, plan_name, difficulty, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $plan_name, $difficulty, $description);
    
    if ($stmt->execute()) {
        $plan_id = $conn->insert_id;
        
        // Insert exercises
        $stmt2 = $conn->prepare("INSERT INTO plan_exercises (plan_id, exercise_name, sets, reps, weight, rest_time, exercise_order, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $order = 1;
        foreach ($exercises as $exercise) {
            $exercise_name = sanitizeInput($exercise['name'] ?? '');
            $sets = intval($exercise['sets'] ?? 3);
            $reps = intval($exercise['reps'] ?? 10);
            $weight = floatval($exercise['weight'] ?? 0);
            $rest_time = intval($exercise['rest'] ?? 60);
            $notes = sanitizeInput($exercise['notes'] ?? '');
            
            $stmt2->bind_param("isiidiss", $plan_id, $exercise_name, $sets, $reps, $weight, $rest_time, $order, $notes);
            $stmt2->execute();
            $order++;
        }
        
        sendResponse(true, 'Workout created successfully', ['plan_id' => $plan_id]);
    } else {
        sendResponse(false, 'Failed to create workout');
    }
}

// ========================================
// GET ALL WORKOUTS
// ========================================
else if ($action === 'getAll') {
    
    $stmt = $conn->prepare("
        SELECT 
            wp.plan_id,
            wp.plan_name,
            wp.difficulty,
            wp.description,
            wp.created_date,
            COUNT(pe.plan_exercise_id) as exercise_count
        FROM workout_plans wp
        LEFT JOIN plan_exercises pe ON wp.plan_id = pe.plan_id
        WHERE wp.user_id = ?
        GROUP BY wp.plan_id
        ORDER BY wp.created_date DESC
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $workouts = [];
    while ($row = $result->fetch_assoc()) {
        $workouts[] = $row;
    }
    
    sendResponse(true, 'Workouts retrieved', $workouts);
}

// ========================================
// GET SINGLE WORKOUT
// ========================================
else if ($action === 'get') {
    
    $plan_id = intval($_GET['plan_id'] ?? 0);
    
    // Get workout plan
    $stmt = $conn->prepare("SELECT * FROM workout_plans WHERE plan_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $plan_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(false, 'Workout not found');
    }
    
    $workout = $result->fetch_assoc();
    
    // Get exercises
    $stmt2 = $conn->prepare("SELECT * FROM plan_exercises WHERE plan_id = ? ORDER BY exercise_order");
    $stmt2->bind_param("i", $plan_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    $exercises = [];
    while ($row = $result2->fetch_assoc()) {
        $exercises[] = $row;
    }
    
    $workout['exercises'] = $exercises;
    
    sendResponse(true, 'Workout retrieved', $workout);
}

// ========================================
// DELETE WORKOUT
// ========================================
else if ($action === 'delete') {
    
    $plan_id = intval($_POST['plan_id'] ?? 0);
    
    // Verify ownership
    $stmt = $conn->prepare("SELECT plan_id FROM workout_plans WHERE plan_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $plan_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(false, 'Workout not found');
    }
    
    // Delete (exercises will be deleted automatically due to CASCADE)
    $stmt2 = $conn->prepare("DELETE FROM workout_plans WHERE plan_id = ?");
    $stmt2->bind_param("i", $plan_id);
    
    if ($stmt2->execute()) {
        sendResponse(true, 'Workout deleted successfully');
    } else {
        sendResponse(false, 'Failed to delete workout');
    }
}

else {
    sendResponse(false, 'Invalid action');
}
?>