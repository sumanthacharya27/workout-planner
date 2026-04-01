<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/auth.php';

try {
    validateRequestMethod('POST');
    $auth->logout();
    success('Logged out', []);
} catch (Throwable $e) {
    errorResponse('Server error', 500);
}
