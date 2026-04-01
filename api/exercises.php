<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

try {
    if (!$auth->isLoggedIn()) {
        errorResponse('Not authenticated', 401);
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $stmt = $db->prepare('SELECT id, name, description, muscle_group, difficulty, instructions FROM exercises ORDER BY muscle_group, difficulty, name');
        $stmt->execute();
        $exercises = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        success('Exercises fetched', ['exercises' => $exercises]);
    }

    if ($method === 'POST') {
        $input = getJSONInput();
        requireFields($input, ['name', 'muscle_group', 'difficulty']);

        $name = sanitizeText($input['name'], 150);
        $description = sanitizeText($input['description'] ?? '', 2000);
        $muscleGroup = sanitizeText($input['muscle_group'], 50);
        $difficulty = sanitizeText($input['difficulty'], 20);
        $instructions = sanitizeText($input['instructions'] ?? '', 4000);

        $stmt = $db->prepare('INSERT INTO exercises (name, description, muscle_group, difficulty, instructions) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $name, $description, $muscleGroup, $difficulty, $instructions);
        $stmt->execute();

        success('Exercise created', ['exercise_id' => $db->lastInsertId()]);
    }

    if ($method === 'PUT') {
        $input = getJSONInput();
        $exerciseId = (int)($input['id'] ?? 0);
        if ($exerciseId <= 0) {
            errorResponse('Exercise ID required', 422);
        }

        $fields = ['name', 'description', 'muscle_group', 'difficulty', 'instructions'];
        $updates = [];
        $params = [];
        $types = '';
        foreach ($fields as $field) {
            if (array_key_exists($field, $input)) {
                $maxLen = $field === 'instructions' ? 4000 : ($field === 'description' ? 2000 : 150);
                $updates[] = "$field = ?";
                $params[] = sanitizeText($input[$field], $maxLen);
                $types .= 's';
            }
        }

        if (empty($updates)) {
            errorResponse('No fields to update', 422);
        }

        $types .= 'i';
        $params[] = $exerciseId;

        $sql = 'UPDATE exercises SET ' . implode(', ', $updates) . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        success('Exercise updated', ['exercise_id' => $exerciseId]);
    }

    if ($method === 'DELETE') {
        $input = getJSONInput();
        $exerciseId = (int)($input['id'] ?? 0);
        if ($exerciseId <= 0) {
            errorResponse('Exercise ID required', 422);
        }

        $stmt = $db->prepare('DELETE FROM exercises WHERE id = ?');
        $stmt->bind_param('i', $exerciseId);
        $stmt->execute();

        success('Exercise deleted', ['exercise_id' => $exerciseId]);
    }

    errorResponse('Method not allowed', 405);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
