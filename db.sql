-- SQL file for GymPlanner database
CREATE DATABASE IF NOT EXISTS gymplanner;
USE gymplanner;

CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workout_id INT,
    name VARCHAR(100) NOT NULL,
    sets INT,
    reps INT,
    weight FLOAT,
    rest INT,
    FOREIGN KEY (workout_id) REFERENCES workouts(id) ON DELETE CASCADE
);
