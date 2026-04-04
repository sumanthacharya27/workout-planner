<?php

function sendJSON(array $payload, int $statusCode = 200): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        http_response_code($statusCode);
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function success(string $message = 'Success', array $data = []): void {
    sendJSON([
        'success' => true,
        'data' => $data,
        'message' => $message,
    ]);
}

function errorResponse(string $message = 'Error', int $statusCode = 400, array $data = []): void {
    sendJSON([
        'success' => false,
        'data' => $data,
        'message' => $message,
    ], $statusCode);
}

function validateRequestMethod(string $method): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== strtoupper($method)) {
        errorResponse('Method not allowed', 405);
    }
}

function getJSONInput(): array {
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        errorResponse('Invalid JSON body', 400);
    }

    return $decoded;
}

function sanitizeText(mixed $value, int $maxLen = 255): string {
    $text = trim((string)($value ?? ''));
    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text) ?? '';
    if ($maxLen > 0) {
        $text = mb_substr($text, 0, $maxLen);
    }
    return $text;
}

function requireFields(array $input, array $fields): void {
    foreach ($fields as $field) {
        if (!array_key_exists($field, $input) || trim((string)$input[$field]) === '') {
            errorResponse("Missing required field: {$field}", 422);
        }
    }
}
