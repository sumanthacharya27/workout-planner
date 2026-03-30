<?php
// Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gym_planner');
define('DB_PORT', 3306);

// App settings
define('APP_NAME', 'GymPlanner Pro');
define('SESSION_TIMEOUT', 86400); // 24 hours
define('API_BASE', '/api/');

// Enable error reporting (development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
