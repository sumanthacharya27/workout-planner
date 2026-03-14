// API Configuration
const API_URL = 'http://localhost/gym-planner/php/';

// API Endpoints
const ENDPOINTS = {
    REGISTER: API_URL + 'auth.php?action=register',
    LOGIN: API_URL + 'auth.php?action=login',
    LOGOUT: API_URL + 'auth.php?action=logout',
    CHECK_SESSION: API_URL + 'auth.php?action=check',
    WORKOUTS: API_URL + 'workouts.php',
    PROGRESS: API_URL + 'progress.php'
};