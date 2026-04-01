<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/auth.php';

// Handle browser visits (GET requests) for testing
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    success('Login API is online. Please use POST to log in.', [
        'endpoint' => 'POST /api/login.php',
        'required_fields' => ['email', 'password']
    ]);
}

try {
    validateRequestMethod('POST');
    
    // Check for JSON input first, then fall back to standard form data
    $input = getJSONInput();
    if (empty($input)) {
        $input = $_POST;
    }

    requireFields($input, ['email', 'password']);

    $email = sanitizeText($input['email']);
    $password = (string)$input['password'];

    $result = $auth->login($email, $password);
    if (!$result['success']) {
        errorResponse($result['message'], 401);
    }

    success('Login successful', [
        'user_id' => $auth->getCurrentUserId(),
        'user_name' => $auth->getCurrentUserName()
    ]);
} catch (Throwable $e) {
    errorResponse('Server error: ' . $e->getMessage(), 500);
}
