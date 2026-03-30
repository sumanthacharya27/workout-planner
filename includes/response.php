<?php
// Helper functions for API responses

function sendJSON($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function success($message = 'Success', $data = null) {
    sendJSON([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

function error($message = 'Error', $statusCode = 400, $data = null) {
    sendJSON([
        'success' => false,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

function validateRequest($method = 'POST') {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        error("Method not allowed", 405);
    }
}

function getJSONInput() {
    return json_decode(file_get_contents('php://input'), true);
}
