<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

function fetchAllTemplates($db) {
    $result = $db->query("SELECT * FROM workout_templates ORDER BY category, name");
    if (!$result) {
        error("Failed to fetch templates", 500);
    }

    $templates = [];
    while ($row = $result->fetch_assoc()) {
        $template_id = $row['id'];

        $exercises_result = $db->query("
            SELECT te.*, e.name, e.description, e.muscle_group, e.difficulty, e.instructions
            FROM template_exercises te
            JOIN exercises e ON te.exercise_id = e.id
            WHERE te.template_id = $template_id
            ORDER BY te.set_group, te.set_order
        ");

        $exercises = [];
        while ($ex_row = $exercises_result->fetch_assoc()) {
            $set_group = $ex_row['set_group'];
            if (!isset($exercises[$set_group])) {
                $exercises[$set_group] = [];
            }
            $exercises[$set_group][] = $ex_row;
        }

        $row['exercise_sets'] = $exercises;
        $row['exercises'] = empty($exercises) ? [] : array_merge(...array_values($exercises));
        $templates[] = $row;
    }

    return $templates;
}

if ($method === 'GET') {
    success("Templates fetched", ['templates' => fetchAllTemplates($db)]);
}

elseif ($method === 'POST') {
    if (!$auth->isAdmin()) {
        error("Admin privileges required", 403);
    }

    $input = getJSONInput();
    if (!isset($input['name'], $input['category'], $input['difficulty'])) {
        error("Missing required fields", 400);
    }

    $name = $db->escape($input['name']);
    $description = $db->escape($input['description'] ?? '');
    $category = $db->escape($input['category']);
    $difficulty = $db->escape($input['difficulty']);
    $estimated_duration = isset($input['estimated_duration']) ? (int)$input['estimated_duration'] : null;

    $durationClause = $estimated_duration ? ", estimated_duration = $estimated_duration" : "";
    $sql = "INSERT INTO workout_templates (name, description, category, difficulty" .
           ($estimated_duration ? ", estimated_duration" : "") . ") VALUES ('{$name}', '{$description}', '{$category}', '{$difficulty}'" .
           ($estimated_duration ? ", $estimated_duration" : "") . ")";

    if (!$db->query($sql)) {
        error("Failed to create template", 500);
    }

    $templateId = $db->lastInsertId();

    if (!empty($input['exercises']) && is_array($input['exercises'])) {
        foreach ($input['exercises'] as $ex) {
            if (!isset($ex['exercise_id'])) continue;
            $exerciseId = (int)$ex['exercise_id'];
            $sets = isset($ex['sets']) ? (int)$ex['sets'] : 3;
            $reps = isset($ex['reps']) ? (int)$ex['reps'] : 10;
            $rest_seconds = isset($ex['rest_seconds']) ? (int)$ex['rest_seconds'] : 60;
            $day_order = isset($ex['day_order']) ? (int)$ex['day_order'] : 1;
            $set_group = isset($ex['set_group']) ? (int)$ex['set_group'] : 1;
            $set_order = isset($ex['set_order']) ? (int)$ex['set_order'] : 1;

            $sqlEx = "INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES ($templateId, $exerciseId, $sets, $reps, $rest_seconds, $day_order, $set_group, $set_order)";
            $db->query($sqlEx);
        }
    }

    success("Template created", ['template_id' => $templateId, 'templates' => fetchAllTemplates($db)]);
}

elseif ($method === 'PUT') {
    if (!$auth->isAdmin()) {
        error("Admin privileges required", 403);
    }

    $input = getJSONInput();
    if (!isset($input['id'])) {
        error("Template ID required", 400);
    }

    $templateId = (int)$input['id'];
    $updates = [];

    foreach (['name', 'description', 'category', 'difficulty'] as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = '" . $db->escape($input[$field]) . "'";
        }
    }
    if (isset($input['estimated_duration'])) {
        $updates[] = "estimated_duration = " . (int)$input['estimated_duration'];
    }

    if (!empty($updates)) {
        $sql = "UPDATE workout_templates SET " . implode(', ', $updates) . " WHERE id = $templateId";
        if (!$db->query($sql)) {
            error("Failed to update template", 500);
        }
    }

    if (isset($input['exercises']) && is_array($input['exercises'])) {
        // delete old and insert new to keep it simple
        $db->query("DELETE FROM template_exercises WHERE template_id = $templateId");
        foreach ($input['exercises'] as $ex) {
            if (!isset($ex['exercise_id'])) continue;
            $exerciseId = (int)$ex['exercise_id'];
            $sets = isset($ex['sets']) ? (int)$ex['sets'] : 3;
            $reps = isset($ex['reps']) ? (int)$ex['reps'] : 10;
            $rest_seconds = isset($ex['rest_seconds']) ? (int)$ex['rest_seconds'] : 60;
            $day_order = isset($ex['day_order']) ? (int)$ex['day_order'] : 1;
            $set_group = isset($ex['set_group']) ? (int)$ex['set_group'] : 1;
            $set_order = isset($ex['set_order']) ? (int)$ex['set_order'] : 1;

            $sqlEx = "INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES ($templateId, $exerciseId, $sets, $reps, $rest_seconds, $day_order, $set_group, $set_order)";
            $db->query($sqlEx);
        }
    }

    success("Template updated", ['template_id' => $templateId, 'templates' => fetchAllTemplates($db)]);
}

elseif ($method === 'DELETE') {
    if (!$auth->isAdmin()) {
        error("Admin privileges required", 403);
    }

    $input = getJSONInput();
    if (!isset($input['id'])) {
        error("Template ID required", 400);
    }

    $templateId = (int)$input['id'];
    $db->query("DELETE FROM template_exercises WHERE template_id = $templateId");
    if (!$db->query("DELETE FROM workout_templates WHERE id = $templateId")) {
        error("Failed to delete template", 500);
    }

    success("Template deleted", ['template_id' => $templateId, 'templates' => fetchAllTemplates($db)]);
}

else {
    error("Method not allowed", 405);
}

