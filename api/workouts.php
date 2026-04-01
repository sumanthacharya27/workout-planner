<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

function requireAuth(Auth $auth): int {
    if (!$auth->isLoggedIn()) {
        errorResponse('Not authenticated', 401);
    }
    return (int)$auth->getCurrentUserId();
}

try {
    $userId = requireAuth($auth);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $stmt = $db->prepare('SELECT id, name, description, created_at FROM custom_workouts WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $workouts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        success('Workouts fetched', ['workouts' => $workouts]);
    }

    if ($method !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $input = getJSONInput();
    $action = sanitizeText($input['action'] ?? '', 30);

    if ($action === 'create') {
        requireFields($input, ['name', 'exercises']);
        if (!is_array($input['exercises']) || count($input['exercises']) === 0) {
            errorResponse('At least one exercise is required', 422);
        }

        $name = sanitizeText($input['name'], 150);
        $description = sanitizeText($input['description'] ?? '', 2000);

        $stmt = $db->prepare('INSERT INTO custom_workouts (user_id, name, description) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $userId, $name, $description);
        $stmt->execute();
        $workoutId = $db->lastInsertId();

        $insertExercise = $db->prepare('INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps, rest_seconds) VALUES (?, ?, ?, ?, ?)');
        foreach ($input['exercises'] as $exercise) {
            $exerciseId = (int)($exercise['exercise_id'] ?? 0);
            $sets = max(1, min(20, (int)($exercise['sets'] ?? 3)));
            $reps = max(1, min(100, (int)($exercise['reps'] ?? 10)));
            $rest = max(0, min(600, (int)($exercise['rest_seconds'] ?? 60)));
            if ($exerciseId <= 0) {
                continue;
            }
            $insertExercise->bind_param('iiiii', $workoutId, $exerciseId, $sets, $reps, $rest);
            $insertExercise->execute();
        }

        success('Workout created', ['workout_id' => $workoutId]);
    }

    if ($action === 'get_detail') {
        $workoutId = (int)($input['workout_id'] ?? 0);
        if ($workoutId <= 0) {
            errorResponse('Invalid workout id', 422);
        }

        $stmt = $db->prepare('SELECT id, name, description, created_at FROM custom_workouts WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->bind_param('ii', $workoutId, $userId);
        $stmt->execute();
        $workout = $stmt->get_result()->fetch_assoc();
        if (!$workout) {
            errorResponse('Workout not found', 404);
        }

        $exStmt = $db->prepare('SELECT we.*, e.name, e.description, e.muscle_group, e.difficulty FROM workout_exercises we JOIN exercises e ON we.exercise_id = e.id WHERE we.workout_id = ? ORDER BY we.id');
        $exStmt->bind_param('i', $workoutId);
        $exStmt->execute();
        $workout['exercises'] = $exStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        success('Workout details fetched', ['workout' => $workout]);
    }

    if ($action === 'get_history') {
        $stmt = $db->prepare('SELECT wh.*, cw.name AS workout_name FROM workout_history wh JOIN custom_workouts cw ON wh.workout_id = cw.id WHERE wh.user_id = ? ORDER BY completed_at DESC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        success('History fetched', ['history' => $history]);
    }

    if ($action === 'complete') {
        $workoutId = (int)($input['workout_id'] ?? 0);
        $duration = max(1, min(1440, (int)($input['duration_minutes'] ?? 1)));
        $notes = sanitizeText($input['notes'] ?? '', 2000);

        $check = $db->prepare('SELECT id FROM custom_workouts WHERE id = ? AND user_id = ? LIMIT 1');
        $check->bind_param('ii', $workoutId, $userId);
        $check->execute();
        if (!$check->get_result()->fetch_assoc()) {
            errorResponse('Workout not found', 404);
        }

        $stmt = $db->prepare('INSERT INTO workout_history (user_id, workout_id, completed_at, duration_minutes, notes) VALUES (?, ?, NOW(), ?, ?)');
        $stmt->bind_param('iiis', $userId, $workoutId, $duration, $notes);
        $stmt->execute();

        success('Workout completed and logged', ['history_id' => $db->lastInsertId()]);
    }

    errorResponse('Unknown action', 400);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
