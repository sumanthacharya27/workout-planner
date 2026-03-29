<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function send_json(bool $success, string $message, $data = null, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
    ]);
    exit;
}

function sanitize_input(?string $value): string
{
    return trim(filter_var((string)$value, FILTER_SANITIZE_SPECIAL_CHARS));
}

function get_json_input(): array
{
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

function require_auth(): int
{
    if (empty($_SESSION['user_id'])) {
        send_json(false, 'Unauthorized.', null, 401);
    }

    return (int)$_SESSION['user_id'];
}

function calculate_streak(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare('SELECT DISTINCT workout_date FROM workout_logs WHERE user_id = :user_id ORDER BY workout_date DESC');
    $stmt->execute(['user_id' => $userId]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$dates) {
        return 0;
    }

    $streak = 0;
    $today = new DateTimeImmutable('today');

    foreach ($dates as $index => $dateValue) {
        $expected = $today->sub(new DateInterval("P{$index}D"))->format('Y-m-d');
        if ($dateValue === $expected) {
            $streak++;
        } else {
            break;
        }
    }

    return $streak;
}
