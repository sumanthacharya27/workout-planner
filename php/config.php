<?php
$host = '127.0.0.1';
$dbName = 'gym_workout_planner';
$dbUser = 'root';
$dbPass = '';

$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.', 'data' => null]);
    exit;
}
