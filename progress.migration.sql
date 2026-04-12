-- ============================================================
-- PROGRESS FIX MIGRATION
-- Run this in phpMyAdmin → your workout_planner database → SQL tab
-- ============================================================

USE workout_planner;

-- Step 1: Add user_id to workout_history (so history is per-user)
ALTER TABLE workout_history
    ADD COLUMN user_id INT NULL AFTER workout_id,
    ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- Step 2: Add user_id to user_stats (so stats are per-user)
ALTER TABLE user_stats
    ADD COLUMN user_id INT NULL UNIQUE AFTER id,
    ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- Step 3: Add index for fast per-user lookups
CREATE INDEX idx_workout_history_user ON workout_history(user_id);

-- Step 4: Update the existing dummy stats row to belong to admin (user id=1)
-- (existing history rows will have NULL user_id - they'll be ignored going forward)
UPDATE user_stats SET user_id = 1 WHERE user_id IS NULL LIMIT 1;

-- Done! Re-open your app and complete a workout to start seeing progress data.
