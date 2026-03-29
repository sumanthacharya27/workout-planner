<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$action = $_GET['action'] ?? '';
$input = get_json_input();

switch ($action) {
    case 'register':
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string)($input['password'] ?? '');
        $name = sanitize_input($input['name'] ?? '');
        $height = isset($input['height']) ? (float)$input['height'] : null;
        $age = isset($input['age']) ? (int)$input['age'] : null;
        $gender = sanitize_input($input['gender'] ?? 'prefer-not-to-say');

        if (!$email || strlen($password) < 6 || $name === '') {
            send_json(false, 'Please provide valid registration details.', null, 422);
        }

        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetch()) {
            send_json(false, 'Email already registered.', null, 409);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $pdo->prepare(
            'INSERT INTO users (email, password_hash, name, height, age, gender) VALUES (:email, :password_hash, :name, :height, :age, :gender)'
        );
        $insertStmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'name' => $name,
            'height' => $height,
            'age' => $age,
            'gender' => $gender,
        ]);

        $userId = (int)$pdo->lastInsertId();
        $statsStmt = $pdo->prepare('INSERT INTO user_stats (user_id) VALUES (:user_id)');
        $statsStmt->execute(['user_id' => $userId]);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;

        send_json(true, 'Registration successful.', ['user' => ['id' => $userId, 'name' => $name, 'email' => $email]]);

    case 'login':
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string)($input['password'] ?? '');

        if (!$email || $password === '') {
            send_json(false, 'Email and password are required.', null, 422);
        }

        $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            send_json(false, 'Invalid credentials.', null, 401);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];

        unset($user['password_hash']);
        send_json(true, 'Login successful.', ['user' => $user]);

    case 'logout':
        session_unset();
        session_destroy();
        send_json(true, 'Logged out successfully.');

    case 'check':
        if (!empty($_SESSION['user_id'])) {
            send_json(true, 'Authenticated.', ['user_id' => (int)$_SESSION['user_id'], 'name' => $_SESSION['user_name'] ?? '']);
        }
        send_json(false, 'Not authenticated.', null, 401);

    default:
        send_json(false, 'Unknown auth action.', null, 400);
}
