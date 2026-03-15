<?php
require_once 'config.php';
require_once 'functions.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// ========================================
// CHECK SESSION - ADD THIS HERE
// ========================================
if (!isset($_SESSION['user_id'])) {
    sendResponse(false, 'Not logged in');
}

$user_id = $_SESSION['user_id'];
// ========================================

$action = $_GET['action'] ?? '';

// ========================================
// GET STATS
// ========================================
if ($action === 'getStats') {
    
    $stmt = $conn->prepare("SELECT * FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stats = $result->fetch_assoc();
        sendResponse(true, 'Stats retrieved', $stats);
    } else {
        sendResponse(true, 'Stats retrieved', [
            'total_workouts' => 0,
            'total_exercises' => 0,
            'total_time' => 0,
            'current_streak' => 0
        ]);
    }
}

// ========================================
// UPDATE STATS
// ========================================
else if ($action === 'updateStats') {
    
    $duration = intval($_POST['duration'] ?? 0);
    $exercises_count = intval($_POST['exercises_count'] ?? 0);
    
    $stmt = $conn->prepare("
        UPDATE user_stats 
        SET total_workouts = total_workouts + 1,
            total_exercises = total_exercises + ?,
            total_time = total_time + ?,
            last_workout_date = CURDATE()
        WHERE user_id = ?
    ");
    $stmt->bind_param("iii", $exercises_count, $duration, $user_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Stats updated');
    } else {
        sendResponse(false, 'Failed to update stats');
    }
}

// ========================================
// SAVE WORKOUT
// ========================================
else if ($action === 'saveWorkout') {
    
    $plan_name = sanitizeInput($_POST['plan_name'] ?? '');
    $difficulty = sanitizeInput($_POST['difficulty'] ?? 'beginner');
    $description = sanitizeInput($_POST['description'] ?? '');
    $exercises = json_decode($_POST['exercises'] ?? '[]', true);
    
    if (empty($plan_name) || empty($exercises)) {
        sendResponse(false, 'Invalid workout data');
    }
    
    // Insert workout
    $stmt = $conn->prepare("INSERT INTO workout_plans (user_id, plan_name, difficulty, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $plan_name, $difficulty, $description);
    
    if ($stmt->execute()) {
        $plan_id = $conn->insert_id;
        
        // Insert exercises
        $stmt2 = $conn->prepare("INSERT INTO plan_exercises (plan_id, exercise_name, sets, reps, weight, rest_time, exercise_order, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $order = 1;
        foreach ($exercises as $ex) {
            $name = sanitizeInput($ex['name'] ?? '');
            $sets = intval($ex['sets'] ?? 3);
            $reps = intval($ex['reps'] ?? 10);
            $weight = floatval($ex['weight'] ?? 0);
            $rest = intval($ex['rest'] ?? 60);
            $notes = sanitizeInput($ex['notes'] ?? '');
            
            $stmt2->bind_param("isiidiss", $plan_id, $name, $sets, $reps, $weight, $rest, $order, $notes);
            $stmt2->execute();
            $order++;
        }
        
        sendResponse(true, 'Workout saved', ['plan_id' => $plan_id]);
    } else {
        sendResponse(false, 'Failed to save workout');
    }
}

// ========================================
// GET WORKOUTS
// ========================================
else if ($action === 'getWorkouts') {
    
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
else if ($action === 'getWorkout') {
    
    $plan_id = intval($_GET['plan_id'] ?? 0);
    
    // Get workout
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
else if ($action === 'deleteWorkout') {
    
    $plan_id = intval($_POST['plan_id'] ?? 0);
    
    $stmt = $conn->prepare("DELETE FROM workout_plans WHERE plan_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $plan_id, $user_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Workout deleted');
    } else {
        sendResponse(false, 'Failed to delete');
    }
}

// ========================================
// SAVE WORKOUT LOG
// ========================================
else if ($action === 'saveLog') {
    
    $workout_name = sanitizeInput($_POST['workout_name'] ?? '');
    $duration = intval($_POST['duration'] ?? 0);
    $exercises = json_decode($_POST['exercises'] ?? '[]', true);
    
    // Insert log
    $stmt = $conn->prepare("INSERT INTO workout_logs (user_id, workout_date, duration) VALUES (?, NOW(), ?)");
    $stmt->bind_param("ii", $user_id, $duration);
    
    if ($stmt->execute()) {
        $log_id = $conn->insert_id;
        
        // Insert exercises
        $stmt2 = $conn->prepare("INSERT INTO log_exercises (log_id, exercise_name, completed_sets, target_sets, weight) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($exercises as $ex) {
            $name = sanitizeInput($ex['name'] ?? '');
            $completed = intval($ex['completedSets'] ?? 0);
            $target = intval($ex['sets'] ?? 0);
            $weight = floatval($ex['weight'] ?? 0);
            
            $stmt2->bind_param("isiid", $log_id, $name, $completed, $target, $weight);
            $stmt2->execute();
        }
        
        // Update the log with workout name
        $stmt3 = $conn->prepare("UPDATE workout_logs SET performance_notes = ? WHERE log_id = ?");
        $stmt3->bind_param("si", $workout_name, $log_id);
        $stmt3->execute();
        
        sendResponse(true, 'Workout logged', ['log_id' => $log_id]);
    } else {
        sendResponse(false, 'Failed to log workout');
    }
}

// ========================================
// GET HISTORY
// ========================================
else if ($action === 'getHistory') {
    
    $filter = $_GET['filter'] ?? 'all';
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
    
    $where = "WHERE wl.user_id = ?";
    
    if ($filter === 'week') {
        $where .= " AND wl.workout_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } else if ($filter === 'month') {
        $where .= " AND wl.workout_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }
    
    $sql = "
        SELECT 
            wl.log_id,
            wl.workout_date,
            wl.duration,
            wl.performance_notes as workout_name,
            COUNT(le.log_exercise_id) as exercise_count
        FROM workout_logs wl
        LEFT JOIN log_exercises le ON wl.log_id = le.log_id
        $where
        GROUP BY wl.log_id
        ORDER BY wl.workout_date DESC
    ";
    
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($limit) {
        $stmt->bind_param("ii", $user_id, $limit);
    } else {
        $stmt->bind_param("i", $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    sendResponse(true, 'History retrieved', $history);
}

else {
    sendResponse(false, 'Invalid action');
}
?>