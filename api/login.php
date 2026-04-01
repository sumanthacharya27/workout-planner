<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/auth.php';

try {
    validateRequestMethod('POST');
    $input = getJSONInput();
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
    errorResponse('Server error', 500);
}
