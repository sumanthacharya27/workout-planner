<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

function fetchAllTemplates(Database $db): array {
    $stmt = $db->prepare('SELECT * FROM workout_templates ORDER BY category, name');
    $stmt->execute();
    $templates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $exerciseStmt = $db->prepare('SELECT te.*, e.name, e.description, e.muscle_group, e.difficulty, e.instructions FROM template_exercises te JOIN exercises e ON te.exercise_id = e.id WHERE te.template_id = ? ORDER BY te.set_group, te.set_order');

    foreach ($templates as &$template) {
        $templateId = (int)$template['id'];
        $exerciseStmt->bind_param('i', $templateId);
        $exerciseStmt->execute();
        $rows = $exerciseStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $sets = [];
        foreach ($rows as $row) {
            $group = (string)$row['set_group'];
            if (!isset($sets[$group])) {
                $sets[$group] = [];
            }
            $sets[$group][] = $row;
        }

        $template['exercise_sets'] = $sets;
        $template['exercises'] = $rows;
    }

    return $templates;
}

try {
    if (!$auth->isLoggedIn()) {
        errorResponse('Not authenticated', 401);
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        success('Templates fetched', ['templates' => fetchAllTemplates($db)]);
    }

    if ($method === 'POST') {
        $input = getJSONInput();
        requireFields($input, ['name', 'category', 'difficulty']);

        $name = sanitizeText($input['name'], 150);
        $description = sanitizeText($input['description'] ?? '', 2000);
        $category = sanitizeText($input['category'], 50);
        $difficulty = sanitizeText($input['difficulty'], 20);
        $estimatedDuration = isset($input['estimated_duration']) ? max(1, min(500, (int)$input['estimated_duration'])) : null;

        $stmt = $db->prepare('INSERT INTO workout_templates (name, description, category, difficulty, estimated_duration) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssi', $name, $description, $category, $difficulty, $estimatedDuration);
        $stmt->execute();
        $templateId = $db->lastInsertId();

        if (!empty($input['exercises']) && is_array($input['exercises'])) {
            $insertEx = $db->prepare('INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($input['exercises'] as $ex) {
                $exerciseId = (int)($ex['exercise_id'] ?? 0);
                if ($exerciseId <= 0) continue;
                $sets = max(1, min(20, (int)($ex['sets'] ?? 3)));
                $reps = max(1, min(100, (int)($ex['reps'] ?? 10)));
                $rest = max(0, min(600, (int)($ex['rest_seconds'] ?? 60)));
                $dayOrder = max(1, (int)($ex['day_order'] ?? 1));
                $setGroup = max(1, (int)($ex['set_group'] ?? 1));
                $setOrder = max(1, (int)($ex['set_order'] ?? 1));
                $insertEx->bind_param('iiiiiiii', $templateId, $exerciseId, $sets, $reps, $rest, $dayOrder, $setGroup, $setOrder);
                $insertEx->execute();
            }
        }

        success('Template created', ['template_id' => $templateId, 'templates' => fetchAllTemplates($db)]);
    }

    if ($method === 'PUT') {
        $input = getJSONInput();
        $templateId = (int)($input['id'] ?? 0);
        if ($templateId <= 0) {
            errorResponse('Template ID required', 422);
        }

        $updates = [];
        $params = [];
        $types = '';
        foreach (['name' => 150, 'description' => 2000, 'category' => 50, 'difficulty' => 20] as $field => $len) {
            if (array_key_exists($field, $input)) {
                $updates[] = "$field = ?";
                $params[] = sanitizeText($input[$field], $len);
                $types .= 's';
            }
        }
        if (array_key_exists('estimated_duration', $input)) {
            $updates[] = 'estimated_duration = ?';
            $params[] = max(1, min(500, (int)$input['estimated_duration']));
            $types .= 'i';
        }

        if (!empty($updates)) {
            $types .= 'i';
            $params[] = $templateId;
            $stmt = $db->prepare('UPDATE workout_templates SET ' . implode(', ', $updates) . ' WHERE id = ?');
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
        }

        if (isset($input['exercises']) && is_array($input['exercises'])) {
            $del = $db->prepare('DELETE FROM template_exercises WHERE template_id = ?');
            $del->bind_param('i', $templateId);
            $del->execute();

            $insertEx = $db->prepare('INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($input['exercises'] as $ex) {
                $exerciseId = (int)($ex['exercise_id'] ?? 0);
                if ($exerciseId <= 0) continue;
                $sets = max(1, min(20, (int)($ex['sets'] ?? 3)));
                $reps = max(1, min(100, (int)($ex['reps'] ?? 10)));
                $rest = max(0, min(600, (int)($ex['rest_seconds'] ?? 60)));
                $dayOrder = max(1, (int)($ex['day_order'] ?? 1));
                $setGroup = max(1, (int)($ex['set_group'] ?? 1));
                $setOrder = max(1, (int)($ex['set_order'] ?? 1));
                $insertEx->bind_param('iiiiiiii', $templateId, $exerciseId, $sets, $reps, $rest, $dayOrder, $setGroup, $setOrder);
                $insertEx->execute();
            }
        }

        success('Template updated', ['template_id' => $templateId, 'templates' => fetchAllTemplates($db)]);
    }

    if ($method === 'DELETE') {
        $input = getJSONInput();
        $templateId = (int)($input['id'] ?? 0);
        if ($templateId <= 0) {
            errorResponse('Template ID required', 422);
        }

        $delEx = $db->prepare('DELETE FROM template_exercises WHERE template_id = ?');
        $delEx->bind_param('i', $templateId);
        $delEx->execute();

        $delTemplate = $db->prepare('DELETE FROM workout_templates WHERE id = ?');
        $delTemplate->bind_param('i', $templateId);
        $delTemplate->execute();

        success('Template deleted', ['template_id' => $templateId, 'templates' => fetchAllTemplates($db)]);
    }

    errorResponse('Method not allowed', 405);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
