<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/auth.php';

validateRequest('POST');
$input = getJSONInput();

if (!isset($input['action'])) {
    error("Action required", 400);
}

$action = $input['action'];

if ($action === 'register') {
    if (!isset($input['email'], $input['password'], $input['name'])) {
        error("Missing required fields", 400);
    }
    
    $result = $auth->register($input['email'], $input['password'], $input['name']);
    if ($result['success']) {
        success($result['message'], $result);
    } else {
        error($result['message'], 400);
    }
}
elseif ($action === 'login') {
    if (!isset($input['email'], $input['password'])) {
        error("Missing required fields", 400);
    }
    
    $result = $auth->login($input['email'], $input['password']);
    if ($result['success']) {
        success($result['message'], [
            'user_id' => $auth->getCurrentUserId(),
            'user_name' => $auth->getCurrentUserName(),
            'role' => $auth->getCurrentUserRole()
        ]);
    } else {
        error($result['message'], 401);
    }
}
elseif ($action === 'logout') {
    $result = $auth->logout();
    success($result['message']);
}
elseif ($action === 'status') {
    if ($auth->isLoggedIn()) {
        success("Logged in", [
            'user_id' => $auth->getCurrentUserId(),
            'user_name' => $auth->getCurrentUserName(),
            'role' => $auth->getCurrentUserRole()
        ]);
    } else {
        error("Not logged in", 401);
    }
}
else {
    error("Unknown action", 400);
}
