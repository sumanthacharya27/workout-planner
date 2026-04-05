<?php
require_once '../../includes/config.php';
require_once '../../includes/response.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

function fetchAdminTemplates(Database $db): array {
    $stmt = $db->prepare('SELECT id, name, description, category, difficulty, estimated_duration, created_at FROM workout_templates ORDER BY created_at DESC');
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

try {
    if (!$auth->isLoggedIn()) {
        errorResponse('Not authenticated', 401);
    }

    if (!isAdmin()) {
        errorResponse('Admin access required', 403);
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        success('Templates fetched', ['templates' => fetchAdminTemplates($db)]);
    }

    if ($method === 'POST') {
        $input = getJSONInput();
        requireFields($input, ['name', 'description']);

        $name = sanitizeText($input['name'], 150);
        $description = sanitizeText($input['description'], 2000);
        $category = 'admin_custom';
        $difficulty = 'Beginner';

        $stmt = $db->prepare('INSERT INTO workout_templates (name, description, category, difficulty) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $description, $category, $difficulty);
        $stmt->execute();

        success('Template added', [
            'template_id' => $db->lastInsertId(),
            'templates' => fetchAdminTemplates($db),
        ]);
    }

    errorResponse('Method not allowed', 405);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
