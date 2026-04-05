<?php
require_once '../../includes/config.php';
require_once '../../includes/response.php';
require_once '../../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    success('Login API is online. Please use POST to log in.', [
        'endpoint' => 'POST /api/auth/login.php',
        'required_fields' => ['email', 'password']
    ]);
}

try {
    validateRequestMethod('POST');

    $input = getJSONInput();
    if (empty($input)) {
        $input = $_POST;
    }

    requireFields($input, ['email', 'password']);

    $result = $auth->login((string)$input['email'], (string)$input['password']);
    if (!$result['success']) {
        errorResponse($result['message'], 401);
    }

    success('Login successful', [
        'user_id' => $auth->getCurrentUserId(),
        'user_name' => $auth->getCurrentUserName(),
        'email' => $auth->getCurrentUserEmail(),
        'is_admin' => isAdmin()
    ]);
} catch (Throwable $e) {
    errorResponse('Server error: ' . $e->getMessage(), 500);
}
