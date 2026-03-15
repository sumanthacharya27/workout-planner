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
// ADD PROGRESS ENTRY
// ========================================
if ($action === 'add') {
    
    $tracking_date = sanitizeInput($_POST['tracking_date'] ?? date('Y-m-d'));
    $weight = floatval($_POST['weight'] ?? 0);
    $chest = floatval($_POST['chest'] ?? 0);
    $waist = floatval($_POST['waist'] ?? 0);
    $hips = floatval($_POST['hips'] ?? 0);
    $thighs = floatval($_POST['thighs'] ?? 0);
    $biceps = floatval($_POST['biceps'] ?? 0);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    $stmt = $conn->prepare("INSERT INTO progress_tracking (user_id, tracking_date, weight, chest, waist, hips, thighs, biceps, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isddddddds", $user_id, $tracking_date, $weight, $chest, $waist, $hips, $thighs, $biceps, $notes);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Progress saved successfully', ['progress_id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Failed to save progress');
    }
}

// ========================================
// GET ALL PROGRESS
// ========================================
else if ($action === 'getAll') {
    
    $stmt = $conn->prepare("SELECT * FROM progress_tracking WHERE user_id = ? ORDER BY tracking_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $progress = [];
    while ($row = $result->fetch_assoc()) {
        $progress[] = $row;
    }
    
    sendResponse(true, 'Progress retrieved', $progress);
}

// ========================================
// GET LATEST WEIGHT
// ========================================
else if ($action === 'getLatestWeight') {
    
    $stmt = $conn->prepare("SELECT weight, tracking_date FROM progress_tracking WHERE user_id = ? AND weight > 0 ORDER BY tracking_date DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        sendResponse(true, 'Latest weight retrieved', $data);
    } else {
        sendResponse(false, 'No weight data found');
    }
}

// ========================================
// GET WEIGHT HISTORY (for charts)
// ========================================
else if ($action === 'getWeightHistory') {
    
    $limit = intval($_GET['limit'] ?? 30);
    
    $stmt = $conn->prepare("SELECT weight, tracking_date FROM progress_tracking WHERE user_id = ? AND weight > 0 ORDER BY tracking_date ASC LIMIT ?");
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    sendResponse(true, 'Weight history retrieved', $history);
}

// ========================================
// DELETE PROGRESS
// ========================================
else if ($action === 'delete') {
    
    $progress_id = intval($_POST['progress_id'] ?? 0);
    
    $stmt = $conn->prepare("DELETE FROM progress_tracking WHERE progress_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $progress_id, $user_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Progress entry deleted');
    } else {
        sendResponse(false, 'Failed to delete');
    }
}

// ========================================
// GET STATS
// ========================================
else if ($action === 'getStats') {
    
    $stmt = $conn->prepare("SELECT * FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stats = $result->fetch_assoc();
        sendResponse(true, 'Stats retrieved', $stats);
    } else {
        // Create default stats
        $stmt2 = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (?)");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        
        sendResponse(true, 'Stats retrieved', [
            'user_id' => $user_id,
            'total_workouts' => 0,
            'total_exercises' => 0,
            'total_time' => 0,
            'current_streak' => 0,
            'last_workout_date' => null
        ]);
    }
}

// ========================================
// UPDATE STATS (called after workout completion)
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

else {
    sendResponse(false, 'Invalid action');
}
?>