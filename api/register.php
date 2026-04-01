<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/auth.php';

// Handle browser visits (GET requests) for testing
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    success('Registration API is online. Please use POST to register.', [
        'endpoint' => 'POST /api/register.php',
        'required_fields' => ['name', 'email', 'password']
    ]);
}

try {
    validateRequestMethod('POST');
    
    // Robust input reading: Use $_POST if filled, else try JSON
    $input = (is_array($_POST) && !empty($_POST)) ? $_POST : [];
    
    if (empty($input)) {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
    }
    
    if (!is_array($input)) {
        $input = [];
    }

    // Required fields check
    $required = ['name', 'email', 'password'];
    $missing = [];
    foreach ($required as $field) {
        $val = isset($input[$field]) ? (string)$input[$field] : '';
        if (trim($val) === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        errorResponse('Fields missing: ' . implode(', ', $missing), 422, [
            'mode' => !empty($_POST) ? 'post_mode' : 'json_mode',
            'received' => array_keys($input)
        ]);
    }

    $result = $auth->register(
        sanitizeText($input['email'] ?? ''),
        (string)($input['password'] ?? ''),
        sanitizeText($input['name'] ?? '', 100)
    );

    if (!$result['success']) {
        errorResponse($result['message'], 422);
    }

    success('Registration successful', ['user_id' => $result['user_id']]);
} catch (Throwable $e) {
    errorResponse('Server error: ' . $e->getMessage(), 500);
}
