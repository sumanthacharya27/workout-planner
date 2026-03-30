<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $db->query("SELECT id, name, description, muscle_group, difficulty, instructions FROM exercises ORDER BY muscle_group, difficulty");
    if (!$result) {
        error("Failed to fetch exercises", 500);
    }

    $exercises = [];
    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }

    success("Exercises fetched", ['exercises' => $exercises]);
}

elseif ($method === 'POST') {
    if (!$auth->isAdmin()) {
        error("Admin privileges required", 403);
    }

    $input = getJSONInput();
    if (!isset($input['name'], $input['muscle_group'], $input['difficulty'])) {
        error("Missing required fields", 400);
    }

    $name = $db->escape($input['name']);
    $description = $db->escape($input['description'] ?? '');
    $muscle_group = $db->escape($input['muscle_group']);
    $difficulty = $db->escape($input['difficulty']);
    $instructions = $db->escape($input['instructions'] ?? '');

    $sql = "INSERT INTO exercises (name, description, muscle_group, difficulty, instructions)
            VALUES ('$name', '$description', '$muscle_group', '$difficulty', '$instructions')";

    if (!$db->query($sql)) {
        error("Failed to create exercise", 500);
    }

    success("Exercise created", ['exercise_id' => $db->lastInsertId()]);
}

elseif ($method === 'PUT') {
    if (!$auth->isAdmin()) {
        error("Admin privileges required", 403);
    }

    $input = getJSONInput();
    if (!isset($input['id'])) {
        error("Exercise ID required", 400);
    }

    $exerciseId = (int)$input['id'];
    $updates = [];

    foreach (['name', 'description', 'muscle_group', 'difficulty', 'instructions'] as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = '" . $db->escape($input[$field]) . "'";
        }
    }

    if (empty($updates)) {
        error("No fields to update", 400);
    }

    $sql = "UPDATE exercises SET " . implode(', ', $updates) . " WHERE id = $exerciseId";

    if (!$db->query($sql)) {
        error("Failed to update exercise", 500);
    }

    success("Exercise updated", ['exercise_id' => $exerciseId]);
}

elseif ($method === 'DELETE') {
    if (!$auth->isAdmin()) {
        error("Admin privileges required", 403);
    }

    $input = getJSONInput();
    if (!isset($input['id'])) {
        error("Exercise ID required", 400);
    }

    $exerciseId = (int)$input['id'];
    $sql = "DELETE FROM exercises WHERE id = $exerciseId";

    if (!$db->query($sql)) {
        error("Failed to delete exercise", 500);
    }

    success("Exercise deleted", ['exercise_id' => $exerciseId]);
}

else {
    error("Method not allowed", 405);
}

