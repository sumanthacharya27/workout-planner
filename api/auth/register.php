<?php
require_once '../../includes/config.php';
require_once '../../includes/response.php';
require_once '../../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    success('Registration API is online. Please use POST to register.', [
        'endpoint' => 'POST /api/auth/register.php',
        'required_fields' => ['name', 'email', 'password']
    ]);
}

try {
    validateRequestMethod('POST');

    $input = (is_array($_POST) && !empty($_POST)) ? $_POST : getJSONInput();
    requireFields($input, ['name', 'email', 'password']);

    $result = $auth->register(
        (string)$input['email'],
        (string)$input['password'],
        (string)$input['name']
    );

    if (!$result['success']) {
        errorResponse($result['message'], 422);
    }

    success('Registration successful', ['user_id' => $result['data']['user_id'] ?? null]);
} catch (Throwable $e) {
    errorResponse('Server error: ' . $e->getMessage(), 500);
}
