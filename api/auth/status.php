<?php
require_once '../../includes/config.php';
require_once '../../includes/response.php';
require_once '../../includes/auth.php';

try {
    validateRequestMethod('GET');
    if (!$auth->isLoggedIn()) {
        errorResponse('Not logged in', 401);
    }

    success('Logged in', [
        'user_id' => $auth->getCurrentUserId(),
        'user_name' => $auth->getCurrentUserName()
    ]);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
