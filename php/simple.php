<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$userId = require_auth();
$action = $_GET['action'] ?? '';
$input = get_json_input();

switch ($action) {
    case 'getStats':
        $statsStmt = $pdo->prepare('SELECT total_workouts, total_exercises, total_time_minutes, streak_days FROM user_stats WHERE user_id = :user_id LIMIT 1');
        $statsStmt->execute(['user_id' => $userId]);
        $stats = $statsStmt->fetch();

        $recentStmt = $pdo->prepare('SELECT id, workout_name, workout_date, duration_minutes FROM workout_logs WHERE user_id = :user_id ORDER BY workout_date DESC, id DESC LIMIT 5');
        $recentStmt->execute(['user_id' => $userId]);
        $recent = $recentStmt->fetchAll();

        send_json(true, 'Stats fetched.', ['stats' => $stats, 'recent' => $recent]);

    case 'updateStats':
        $totalWorkouts = (int)($input['total_workouts'] ?? 0);
        $totalExercises = (int)($input['total_exercises'] ?? 0);
        $totalTimeMinutes = (int)($input['total_time_minutes'] ?? 0);
        $streakDays = (int)($input['streak_days'] ?? 0);

        $stmt = $pdo->prepare('UPDATE user_stats SET total_workouts = :tw, total_exercises = :te, total_time_minutes = :tt, streak_days = :sd WHERE user_id = :user_id');
        $stmt->execute([
            'tw' => $totalWorkouts,
            'te' => $totalExercises,
            'tt' => $totalTimeMinutes,
            'sd' => $streakDays,
            'user_id' => $userId,
        ]);

        send_json(true, 'Stats updated.');

    case 'saveWorkout':
        $name = sanitize_input($input['name'] ?? '');
        $difficulty = sanitize_input($input['difficulty'] ?? 'Beginner');
        $description = sanitize_input($input['description'] ?? '');
        $exercises = $input['exercises'] ?? [];

        if ($name === '' || !is_array($exercises) || !$exercises) {
            send_json(false, 'Workout name and exercises are required.', null, 422);
        }

        $pdo->beginTransaction();
        try {
            $planStmt = $pdo->prepare('INSERT INTO workout_plans (user_id, name, difficulty, description, is_premade) VALUES (:user_id, :name, :difficulty, :description, 0)');
            $planStmt->execute([
                'user_id' => $userId,
                'name' => $name,
                'difficulty' => in_array($difficulty, ['Beginner', 'Intermediate', 'Advanced'], true) ? $difficulty : 'Beginner',
                'description' => $description,
            ]);

            $planId = (int)$pdo->lastInsertId();
            $exerciseStmt = $pdo->prepare('INSERT INTO plan_exercises (plan_id, name, sets, reps, weight, rest_seconds, exercise_order) VALUES (:plan_id, :name, :sets, :reps, :weight, :rest_seconds, :exercise_order)');

            foreach ($exercises as $index => $exercise) {
                $exerciseStmt->execute([
                    'plan_id' => $planId,
                    'name' => sanitize_input($exercise['name'] ?? ''),
                    'sets' => (int)($exercise['sets'] ?? 3),
                    'reps' => (int)($exercise['reps'] ?? 10),
                    'weight' => (float)($exercise['weight'] ?? 0),
                    'rest_seconds' => (int)($exercise['rest'] ?? 60),
                    'exercise_order' => $index + 1,
                ]);
            }

            $pdo->commit();
            send_json(true, 'Workout saved.', ['plan_id' => $planId]);
        } catch (Throwable $throwable) {
            $pdo->rollBack();
            send_json(false, 'Could not save workout.', null, 500);
        }

    case 'getWorkouts':
        $stmt = $pdo->prepare('SELECT id, name, difficulty, description, created_at FROM workout_plans WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        $plans = $stmt->fetchAll();

        $exerciseStmt = $pdo->prepare('SELECT name, sets, reps, weight, rest_seconds, exercise_order FROM plan_exercises WHERE plan_id = :plan_id ORDER BY exercise_order ASC');
        foreach ($plans as &$plan) {
            $exerciseStmt->execute(['plan_id' => $plan['id']]);
            $plan['exercises'] = $exerciseStmt->fetchAll();
        }

        send_json(true, 'Workouts fetched.', $plans);

    case 'deleteWorkout':
        $planId = (int)($input['plan_id'] ?? 0);

        $stmt = $pdo->prepare('DELETE FROM workout_plans WHERE id = :plan_id AND user_id = :user_id');
        $stmt->execute(['plan_id' => $planId, 'user_id' => $userId]);

        if ($stmt->rowCount() === 0) {
            send_json(false, 'Workout not found.', null, 404);
        }

        send_json(true, 'Workout deleted.');

    case 'saveLog':
        $workoutName = sanitize_input($input['workout_name'] ?? 'Workout');
        $durationMinutes = (int)($input['duration_minutes'] ?? 0);
        $date = sanitize_input($input['date'] ?? date('Y-m-d'));
        $exercises = $input['exercises'] ?? [];
        $planId = isset($input['workout_plan_id']) ? (int)$input['workout_plan_id'] : null;

        if ($durationMinutes <= 0) {
            send_json(false, 'Duration must be greater than zero.', null, 422);
        }

        $pdo->beginTransaction();
        try {
            $logStmt = $pdo->prepare('INSERT INTO workout_logs (user_id, workout_plan_id, workout_name, workout_date, duration_minutes) VALUES (:user_id, :plan_id, :workout_name, :workout_date, :duration_minutes)');
            $logStmt->execute([
                'user_id' => $userId,
                'plan_id' => $planId,
                'workout_name' => $workoutName,
                'workout_date' => $date,
                'duration_minutes' => $durationMinutes,
            ]);

            $logId = (int)$pdo->lastInsertId();
            $exerciseStmt = $pdo->prepare('INSERT INTO log_exercises (log_id, name, sets, reps, weight, rest_seconds, exercise_order) VALUES (:log_id, :name, :sets, :reps, :weight, :rest_seconds, :exercise_order)');
            foreach ($exercises as $index => $exercise) {
                $exerciseStmt->execute([
                    'log_id' => $logId,
                    'name' => sanitize_input($exercise['name'] ?? ''),
                    'sets' => (int)($exercise['sets'] ?? 0),
                    'reps' => (int)($exercise['reps'] ?? 0),
                    'weight' => (float)($exercise['weight'] ?? 0),
                    'rest_seconds' => (int)($exercise['rest'] ?? 0),
                    'exercise_order' => $index + 1,
                ]);
            }

            $exerciseCount = count($exercises);
            $streakDays = calculate_streak($pdo, $userId);
            $statsUpdate = $pdo->prepare('UPDATE user_stats SET total_workouts = total_workouts + 1, total_exercises = total_exercises + :exercise_count, total_time_minutes = total_time_minutes + :duration, streak_days = :streak WHERE user_id = :user_id');
            $statsUpdate->execute([
                'exercise_count' => $exerciseCount,
                'duration' => $durationMinutes,
                'streak' => $streakDays,
                'user_id' => $userId,
            ]);

            $pdo->commit();
            send_json(true, 'Workout log saved.', ['log_id' => $logId]);
        } catch (Throwable $throwable) {
            $pdo->rollBack();
            send_json(false, 'Failed to save workout log.', null, 500);
        }

    case 'getHistory':
        $filter = sanitize_input($_GET['filter'] ?? 'all');
        $query = 'SELECT id, workout_name, workout_date, duration_minutes FROM workout_logs WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($filter === 'week') {
            $query .= ' AND workout_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        } elseif ($filter === 'month') {
            $query .= ' AND workout_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
        }

        $query .= ' ORDER BY workout_date DESC, id DESC';
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        send_json(true, 'History fetched.', $logs);

    default:
        send_json(false, 'Unknown action.', null, 400);
}
