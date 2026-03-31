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

CREATE TABLE IF NOT EXISTS workout_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL, -- 'bro_split', 'push_pull_legs', 'full_body', etc.
    difficulty VARCHAR(20) NOT NULL,
    estimated_duration INT, -- minutes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS template_exercises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT NOT NULL DEFAULT 3,
    reps INT NOT NULL DEFAULT 10,
    rest_seconds INT DEFAULT 60,
    notes TEXT,
    day_order INT DEFAULT 1, -- For multi-day splits
    set_group INT DEFAULT 1, -- Groups exercises that should be performed together (superset/circuit)
    set_order INT DEFAULT 1, -- Order within the set group
    FOREIGN KEY (template_id) REFERENCES workout_templates(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
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
('Pull-ups', 'Upper body pulling exercise', 'Back', 'Advanced', 'Pull chin above bar, lower yourself with control'),
('Lunges', 'Single leg exercise', 'Legs', 'Beginner', 'Step forward, lower until both knees are bent at 90 degrees'),
('Dips', 'Bodyweight tricep exercise', 'Triceps', 'Intermediate', 'Lower body between parallel bars, push back up'),
('Lat Pulldowns', 'Back exercise with machine', 'Back', 'Beginner', 'Pull bar down to chest, squeeze shoulder blades'),
('Leg Curls', 'Hamstring exercise', 'Legs', 'Beginner', 'Curl weight with legs, control the movement'),
('Calf Raises', 'Calf muscle exercise', 'Calves', 'Beginner', 'Rise up on toes, lower slowly'),
('Overhead Press', 'Shoulder compound', 'Shoulders', 'Intermediate', 'Press weight overhead from shoulder level'),
('Face Pulls', 'Rear shoulder exercise', 'Shoulders', 'Beginner', 'Pull rope towards face, elbows high'),
('Russian Twists', 'Core rotation exercise', 'Core', 'Beginner', 'Rotate torso side to side while holding weight'),
('Hammer Curls', 'Bicep variation', 'Biceps', 'Beginner', 'Curl with neutral grip, control descent'),
('Tricep Extensions', 'Tricep isolation', 'Triceps', 'Beginner', 'Extend arms overhead, lower behind head');

-- Insert premade workout templates
INSERT INTO workout_templates (name, description, category, difficulty, estimated_duration) VALUES
('Bro Split - Chest Day', 'Focus on chest development with compound and isolation movements', 'bro_split', 'Intermediate', 45),
('Bro Split - Back Day', 'Build a strong back with pulling movements', 'bro_split', 'Intermediate', 50),
('Bro Split - Leg Day', 'Complete lower body development', 'bro_split', 'Intermediate', 55),
('Bro Split - Shoulder Day', 'Develop strong, balanced shoulders', 'bro_split', 'Intermediate', 40),
('Bro Split - Arm Day', 'Bicep and tricep focused workout', 'bro_split', 'Intermediate', 45),
('Push/Pull/Legs - Push Day', 'Chest, shoulders, and triceps focus', 'push_pull_legs', 'Intermediate', 50),
('Push/Pull/Legs - Pull Day', 'Back and biceps focus', 'push_pull_legs', 'Intermediate', 50),
('Push/Pull/Legs - Legs Day', 'Complete lower body development', 'push_pull_legs', 'Intermediate', 60),
('Full Body Beginner', 'Complete workout for beginners', 'full_body', 'Beginner', 40),
('Upper/Lower Split - Upper', 'Upper body focus day', 'upper_lower', 'Intermediate', 50),
('Upper/Lower Split - Lower', 'Lower body focus day', 'upper_lower', 'Intermediate', 55),
('4-Day Split - Day 1', 'Chest and triceps focus', '4_day_split', 'Intermediate', 45),
('4-Day Split - Day 2', 'Back and biceps focus', '4_day_split', 'Intermediate', 45),
('4-Day Split - Day 3', 'Legs focus', '4_day_split', 'Intermediate', 50),
('4-Day Split - Day 4', 'Shoulders and core focus', '4_day_split', 'Intermediate', 40);

-- Insert template exercises for each workout
-- Bro Split - Chest Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(1, 4, 4, 8, 90, 1, 1, 1), -- Bench Press (primary chest exercise)
(1, 1, 3, 12, 60, 1, 1, 2), -- Push-ups (chest assistance)
(1, 12, 3, 10, 75, 1, 1, 3), -- Dips (chest/triceps compound)
(1, 6, 3, 12, 60, 1, 2, 1); -- Dumbbell Curls (arm balance)

-- Bro Split - Back Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(2, 3, 4, 6, 120, 1, 1, 1), -- Deadlifts (primary compound)
(2, 5, 3, 10, 90, 1, 1, 2), -- Barbell Rows (horizontal pull)
(2, 13, 3, 12, 60, 1, 1, 3), -- Lat Pulldowns (vertical pull)
(2, 10, 3, 8, 90, 1, 1, 4); -- Pull-ups (bodyweight vertical pull)

-- Bro Split - Leg Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(3, 2, 4, 8, 120, 1, 1, 1), -- Squats (primary compound)
(3, 8, 3, 10, 90, 1, 1, 2), -- Leg Press (quad focus)
(3, 11, 3, 10, 60, 1, 1, 3), -- Lunges (unilateral work)
(3, 14, 4, 15, 45, 1, 1, 4), -- Leg Curls (hamstring focus)
(3, 15, 3, 20, 30, 1, 1, 5); -- Calf Raises (calf work)

-- Bro Split - Shoulder Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(4, 9, 4, 8, 90, 1, 1, 1), -- Shoulder Press (primary compound)
(4, 16, 3, 10, 75, 1, 1, 2), -- Overhead Press (variation)
(4, 17, 3, 12, 60, 1, 1, 3), -- Face Pulls (rear delt work)
(4, 7, 3, 45, 30, 1, 2, 1); -- Plank (core stability)

-- Bro Split - Arm Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(5, 6, 4, 10, 60, 1, 1, 1), -- Dumbbell Curls (bicep isolation)
(5, 19, 3, 12, 60, 1, 1, 2), -- Hammer Curls (bicep variation)
(5, 12, 3, 10, 75, 1, 2, 1), -- Dips (triceps compound)
(5, 20, 3, 12, 60, 1, 2, 2); -- Tricep Extensions (triceps isolation)

-- Push/Pull/Legs - Push Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(6, 4, 4, 8, 90, 1, 1, 1), -- Bench Press (chest)
(6, 9, 3, 10, 75, 1, 1, 2), -- Shoulder Press (shoulders)
(6, 12, 3, 10, 60, 1, 1, 3), -- Dips (triceps)
(6, 17, 3, 12, 45, 1, 1, 4); -- Face Pulls (rear shoulders)

-- Push/Pull/Legs - Pull Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(7, 3, 4, 6, 120, 1, 1, 1), -- Deadlifts (posterior chain)
(7, 5, 3, 10, 90, 1, 1, 2), -- Barbell Rows (back)
(7, 13, 3, 12, 60, 1, 1, 3), -- Lat Pulldowns (back)
(7, 6, 3, 12, 60, 1, 1, 4); -- Dumbbell Curls (biceps)

-- Push/Pull/Legs - Legs Day
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(8, 2, 4, 8, 120, 1, 1, 1), -- Squats (primary compound)
(8, 8, 3, 10, 90, 1, 1, 2), -- Leg Press (quad focus)
(8, 11, 3, 10, 60, 1, 1, 3), -- Lunges (unilateral work)
(8, 14, 4, 15, 45, 1, 1, 4), -- Leg Curls (hamstring focus)
(8, 15, 3, 20, 30, 1, 1, 5); -- Calf Raises (calf work)

-- Full Body Beginner
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(9, 1, 3, 10, 60, 1, 1, 1), -- Push-ups (chest)
(9, 2, 3, 12, 75, 1, 1, 2), -- Squats (legs)
(9, 5, 3, 10, 60, 1, 1, 3), -- Barbell Rows (back)
(9, 7, 3, 30, 30, 1, 1, 4), -- Plank (core)
(9, 6, 3, 12, 45, 1, 1, 5); -- Dumbbell Curls (arms)

-- Upper/Lower Split - Upper
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(10, 4, 4, 8, 90, 1, 1, 1), -- Bench Press (chest)
(10, 5, 3, 10, 90, 1, 1, 2), -- Barbell Rows (back)
(10, 9, 3, 10, 75, 1, 1, 3), -- Shoulder Press (shoulders)
(10, 6, 3, 12, 60, 1, 1, 4), -- Dumbbell Curls (biceps)
(10, 12, 3, 10, 60, 1, 1, 5); -- Dips (triceps)

-- Upper/Lower Split - Lower
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(11, 2, 4, 8, 120, 1, 1, 1), -- Squats (primary compound)
(11, 3, 3, 6, 120, 1, 1, 2), -- Deadlifts (posterior chain)
(11, 11, 3, 10, 60, 1, 1, 3), -- Lunges (unilateral work)
(11, 14, 4, 15, 45, 1, 1, 4), -- Leg Curls (hamstring focus)
(11, 15, 3, 20, 30, 1, 1, 5); -- Calf Raises (calf work)

-- 4-Day Split - Day 1 (Chest & Triceps)
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(12, 4, 4, 8, 90, 1, 1, 1), -- Bench Press (chest)
(12, 1, 3, 12, 60, 1, 1, 2), -- Push-ups (chest)
(12, 12, 3, 10, 75, 1, 2, 1), -- Dips (triceps)
(12, 20, 3, 12, 60, 1, 2, 2); -- Tricep Extensions (triceps)

-- 4-Day Split - Day 2 (Back & Biceps)
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(13, 3, 4, 6, 120, 1, 1, 1), -- Deadlifts (back compound)
(13, 5, 3, 10, 90, 1, 1, 2), -- Barbell Rows (back)
(13, 6, 3, 12, 60, 1, 2, 1), -- Dumbbell Curls (biceps)
(13, 19, 3, 12, 60, 1, 2, 2); -- Hammer Curls (biceps)

-- 4-Day Split - Day 3 (Legs)
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(14, 2, 4, 8, 120, 1, 1, 1), -- Squats (primary compound)
(14, 8, 3, 10, 90, 1, 1, 2), -- Leg Press (quad focus)
(14, 11, 3, 10, 60, 1, 1, 3), -- Lunges (unilateral work)
(14, 14, 4, 15, 45, 1, 1, 4), -- Leg Curls (hamstring focus)
(14, 15, 3, 20, 30, 1, 1, 5); -- Calf Raises (calf work)

-- 4-Day Split - Day 4 (Shoulders & Core)
INSERT INTO template_exercises (template_id, exercise_id, sets, reps, rest_seconds, day_order, set_group, set_order) VALUES
(15, 9, 4, 8, 90, 1, 1, 1), -- Shoulder Press (shoulders)
(15, 16, 3, 10, 75, 1, 1, 2), -- Overhead Press (shoulders)
(15, 17, 3, 12, 60, 1, 1, 3), -- Face Pulls (shoulders)
(15, 7, 3, 45, 30, 1, 2, 1); -- Plank (core)
