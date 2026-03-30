-- GymPlanner Pro Database Schema

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exercises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    muscle_group VARCHAR(50) NOT NULL,
    difficulty VARCHAR(20) NOT NULL,
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS custom_workouts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS workout_exercises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workout_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT NOT NULL DEFAULT 3,
    reps INT NOT NULL DEFAULT 10,
    rest_seconds INT DEFAULT 60,
    notes TEXT,
    FOREIGN KEY (workout_id) REFERENCES custom_workouts(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS workout_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    workout_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duration_minutes INT,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (workout_id) REFERENCES custom_workouts(id) ON DELETE CASCADE
);

-- Insert sample exercises (premade library)
INSERT INTO exercises (name, description, muscle_group, difficulty, instructions) VALUES
('Push-ups', 'Classic bodyweight exercise', 'Chest', 'Beginner', 'Keep your body straight and lower yourself until chest nearly touches ground'),
('Squats', 'Lower body compound exercise', 'Legs', 'Beginner', 'Keep chest up, lower hips until thighs are parallel to ground'),
('Deadlifts', 'Full body compound movement', 'Back', 'Intermediate', 'Keep back straight, lift bar from ground to hip level'),
('Bench Press', 'Chest and triceps exercise', 'Chest', 'Intermediate', 'Lower bar to chest, press back up explosively'),
('Barbell Rows', 'Back strengthening exercise', 'Back', 'Intermediate', 'Pull bar to chest, squeeze shoulders together'),
('Dumbbell Curls', 'Bicep isolation exercise', 'Biceps', 'Beginner', 'Curl weights up, control the descent'),
('Plank', 'Core stability exercise', 'Core', 'Beginner', 'Hold push-up position, keep body straight'),
('Leg Press', 'Lower body compound', 'Legs', 'Beginner', 'Push platform away using legs, control descent'),
('Shoulder Press', 'Shoulder exercise', 'Shoulders', 'Intermediate', 'Press weight overhead, lower to shoulder level'),
('Pull-ups', 'Upper body pulling exercise', 'Back', 'Advanced', 'Pull chin above bar, lower yourself with control');
