<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/auth.php';

try {
    validateRequestMethod('POST');
    $input = getJSONInput();
    requireFields($input, ['name', 'email', 'password']);

    $result = $auth->register(
        sanitizeText($input['email']),
        (string)$input['password'],
        sanitizeText($input['name'], 100)
    );

    if (!$result['success']) {
        errorResponse($result['message'], 422);
    }

    success('Registration successful', ['user_id' => $result['user_id']]);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
