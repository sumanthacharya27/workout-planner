-- ============================================
-- GYM PLANNER DATABASE SCHEMA
-- ============================================
-- Import this file into phpMyAdmin or MySQL

-- Create Database
CREATE DATABASE IF NOT EXISTS workout_planner;
USE workout_planner;

-- ============================================
-- USERS TABLE (Admin only)
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/xya', 'admin');

-- ============================================
-- WORKOUTS TABLE
-- ============================================
CREATE TABLE workouts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    difficulty VARCHAR(20) DEFAULT 'beginner',
    is_custom BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EXERCISES TABLE
-- ============================================
CREATE TABLE exercises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workout_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    sets INT DEFAULT 3,
    reps INT DEFAULT 10,
    weight DECIMAL(5, 2) DEFAULT 0,
    rest_time INT DEFAULT 60,
    notes TEXT,
    exercise_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workout_id) REFERENCES workouts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- WORKOUT HISTORY TABLE
-- ============================================
CREATE TABLE workout_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workout_id INT NOT NULL,
    user_id INT NULL,
    duration INT,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (workout_id) REFERENCES workouts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER STATS TABLE
-- ============================================
CREATE TABLE user_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL UNIQUE,
    total_workouts INT DEFAULT 0,
    total_exercises INT DEFAULT 0,
    total_time INT DEFAULT 0,
    current_streak INT DEFAULT 0,
    last_workout_date DATE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initialize stats
-- Initialize stats for admin
INSERT INTO user_stats (user_id, total_workouts, total_exercises, total_time, current_streak) VALUES (1, 0, 0, 0, 0);

-- ============================================
-- CREATE INDEXES FOR BETTER PERFORMANCE
-- ============================================
CREATE INDEX idx_workout_id ON exercises(workout_id);
CREATE INDEX idx_workout_history_workout_id ON workout_history(workout_id);
CREATE INDEX idx_workout_history_user ON workout_history(user_id);
CREATE INDEX idx_workout_history_date ON workout_history(completed_at);
CREATE INDEX idx_workouts_difficulty ON workouts(difficulty);

-- ============================================
-- SAMPLE PRE-MADE WORKOUTS (Initial Data)
-- ============================================

-- Beginner Full Body
INSERT INTO workouts (name, description, difficulty, is_custom, created_by) VALUES 
('Beginner Full Body', 'A complete full-body workout perfect for beginners', 'beginner', FALSE, 1);
SET @workout_id = LAST_INSERT_ID();
INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, exercise_order) VALUES
(@workout_id, 'Squats', 3, 12, 0, 60, 1),
(@workout_id, 'Push-ups', 3, 10, 0, 60, 2),
(@workout_id, 'Dumbbell Rows', 3, 12, 10, 60, 3),
(@workout_id, 'Plank', 3, 30, 0, 45, 4),
(@workout_id, 'Lunges', 3, 10, 0, 60, 5);

-- Upper Body Strength
INSERT INTO workouts (name, description, difficulty, is_custom, created_by) VALUES 
('Upper Body Strength', 'Build strength in your chest, back, and arms', 'intermediate', FALSE, 1);
SET @workout_id = LAST_INSERT_ID();
INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, exercise_order) VALUES
(@workout_id, 'Bench Press', 4, 8, 60, 90, 1),
(@workout_id, 'Pull-ups', 4, 8, 0, 90, 2),
(@workout_id, 'Overhead Press', 3, 10, 40, 75, 3),
(@workout_id, 'Barbell Rows', 4, 10, 50, 75, 4),
(@workout_id, 'Bicep Curls', 3, 12, 15, 60, 5),
(@workout_id, 'Tricep Dips', 3, 12, 0, 60, 6);

-- Leg Day Power
INSERT INTO workouts (name, description, difficulty, is_custom, created_by) VALUES 
('Leg Day Power', 'Intense leg workout for building lower body strength', 'intermediate', FALSE, 1);
SET @workout_id = LAST_INSERT_ID();
INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, exercise_order) VALUES
(@workout_id, 'Barbell Squats', 5, 5, 80, 120, 1),
(@workout_id, 'Romanian Deadlifts', 4, 8, 70, 90, 2),
(@workout_id, 'Leg Press', 4, 12, 100, 90, 3),
(@workout_id, 'Walking Lunges', 3, 12, 20, 75, 4),
(@workout_id, 'Leg Curls', 3, 15, 30, 60, 5),
(@workout_id, 'Calf Raises', 4, 20, 40, 60, 6);

-- HIIT Cardio Blast
INSERT INTO workouts (name, description, difficulty, is_custom, created_by) VALUES 
('HIIT Cardio Blast', 'High-intensity interval training for maximum calorie burn', 'advanced', FALSE, 1);
SET @workout_id = LAST_INSERT_ID();
INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, exercise_order) VALUES
(@workout_id, 'Burpees', 4, 15, 0, 30, 1),
(@workout_id, 'Mountain Climbers', 4, 30, 0, 30, 2),
(@workout_id, 'Jump Squats', 4, 20, 0, 30, 3),
(@workout_id, 'High Knees', 4, 40, 0, 30, 4),
(@workout_id, 'Box Jumps', 3, 15, 0, 45, 5),
(@workout_id, 'Sprint Intervals', 6, 30, 0, 60, 6);

-- Core Strength
INSERT INTO workouts (name, description, difficulty, is_custom, created_by) VALUES 
('Core Strength', 'Strengthen your core with these targeted exercises', 'beginner', FALSE, 1);
SET @workout_id = LAST_INSERT_ID();
INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, exercise_order) VALUES
(@workout_id, 'Plank', 3, 60, 0, 45, 1),
(@workout_id, 'Russian Twists', 3, 20, 10, 45, 2),
(@workout_id, 'Bicycle Crunches', 3, 20, 0, 45, 3),
(@workout_id, 'Dead Bug', 3, 15, 0, 45, 4),
(@workout_id, 'Leg Raises', 3, 12, 0, 45, 5);

-- Powerlifting Basics
INSERT INTO workouts (name, description, difficulty, is_custom, created_by) VALUES 
('Powerlifting Basics', 'Master the big three: squat, bench, deadlift', 'advanced', FALSE, 1);
SET @workout_id = LAST_INSERT_ID();
INSERT INTO exercises (workout_id, name, sets, reps, weight, rest_time, exercise_order) VALUES
(@workout_id, 'Barbell Squat', 5, 3, 100, 180, 1),
(@workout_id, 'Bench Press', 5, 3, 80, 180, 2),
(@workout_id, 'Deadlift', 5, 3, 120, 180, 3),
(@workout_id, 'Overhead Press', 3, 5, 50, 120, 4);
