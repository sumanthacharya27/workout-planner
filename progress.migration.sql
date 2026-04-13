-- ============================================================
-- SAFE PROGRESS FIX MIGRATION (NO DUPLICATE ERRORS)
-- ============================================================

USE workout_planner;

-- ------------------------------------------------------------
-- Step 1: Ensure user_id exists in workout_history
-- ------------------------------------------------------------
ALTER TABLE workout_history 
ADD COLUMN IF NOT EXISTS user_id INT NULL;

-- Add foreign key (ignore error if already exists)
ALTER TABLE workout_history
ADD CONSTRAINT fk_workout_user
FOREIGN KEY (user_id) REFERENCES users(id);

-- ------------------------------------------------------------
-- Step 2: Ensure user_id exists in user_stats
-- ------------------------------------------------------------
ALTER TABLE user_stats 
ADD COLUMN IF NOT EXISTS user_id INT NULL UNIQUE;

-- Add foreign key (ignore if exists)
ALTER TABLE user_stats
ADD CONSTRAINT fk_stats_user
FOREIGN KEY (user_id) REFERENCES users(id);

-- ------------------------------------------------------------
-- Step 3: Add index for performance
-- ------------------------------------------------------------
CREATE INDEX idx_workout_history_user 
ON workout_history(user_id);

-- ------------------------------------------------------------
-- Step 4: Assign existing stats row to admin (id = 1)
-- ------------------------------------------------------------
UPDATE user_stats 
SET user_id = 1 
WHERE user_id IS NULL 
LIMIT 1;

-- ============================================================
-- DONE ✅
-- ============================================================