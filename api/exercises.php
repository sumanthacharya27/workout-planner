<?php
require_once '../includes/config.php';
require_once '../includes/response.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

validateRequest('GET');

// Get all exercises (premade library)
$result = $db->query("SELECT id, name, description, muscle_group, difficulty FROM exercises ORDER BY muscle_group, difficulty");

if (!$result) {
    error("Failed to fetch exercises", 500);
}

$exercises = [];
while ($row = $result->fetch_assoc()) {
    $exercises[] = $row;
}

success("Exercises fetched", ['exercises' => $exercises]);
