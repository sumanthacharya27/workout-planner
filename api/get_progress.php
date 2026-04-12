<?php
// ============================================
// GET PROGRESS DATA API
// ============================================
// api/get_progress.php

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/db.php';

requireAuth();

$userId = (int)$_SESSION['user_id'];
$range  = $_GET['range'] ?? '30';
$days   = in_array((int)$range, [7, 30, 90, 365]) ? (int)$range : 30;

try {

    // ── 1. Workouts per day for the line chart ────────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            DATE(wh.completed_at) AS workout_date,
            COUNT(*)              AS workout_count,
            SUM(wh.duration)      AS total_duration
        FROM workout_history wh
        WHERE wh.user_id = ?
          AND wh.completed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY DATE(wh.completed_at)
        ORDER BY workout_date ASC
    ");
    $stmt->execute([$userId, $days]);
    $dailyData = $stmt->fetchAll();

    // ── 2. Duration per session for the bar chart ─────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            wh.id,
            w.name        AS workout_name,
            wh.duration,
            wh.completed_at
        FROM workout_history wh
        JOIN workouts w ON w.id = wh.workout_id
        WHERE wh.user_id = ?
          AND wh.completed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        ORDER BY wh.completed_at ASC
        LIMIT 20
    ");
    $stmt->execute([$userId, $days]);
    $sessionData = $stmt->fetchAll();

    // ── 3. Difficulty split for the doughnut chart ────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            w.difficulty,
            COUNT(*) AS count
        FROM workout_history wh
        JOIN workouts w ON w.id = wh.workout_id
        WHERE wh.user_id = ?
          AND wh.completed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY w.difficulty
    ");
    $stmt->execute([$userId, $days]);
    $difficultyDist = $stmt->fetchAll();

    // ── 4. Favourite workouts for the doughnut chart ──────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            w.name,
            COUNT(*) AS count
        FROM workout_history wh
        JOIN workouts w ON w.id = wh.workout_id
        WHERE wh.user_id = ?
          AND wh.completed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY w.id, w.name
        ORDER BY count DESC
        LIMIT 6
    ");
    $stmt->execute([$userId, $days]);
    $workoutDist = $stmt->fetchAll();

    // ── 5. Personal records — best weight per exercise this user has done ─────
    $stmt = $pdo->prepare("
        SELECT
            e.name            AS exercise_name,
            MAX(e.weight)     AS best_weight,
            e.sets            AS sets,
            e.reps            AS reps,
            MAX(e.sets * e.reps * e.weight) AS best_volume
        FROM exercises e
        JOIN workouts w ON w.id = e.workout_id
        JOIN workout_history wh ON wh.workout_id = w.id
        WHERE wh.user_id = ?
          AND e.weight > 0
        GROUP BY e.name
        ORDER BY best_weight DESC
        LIMIT 10
    ");
    $stmt->execute([$userId]);
    $personalRecords = $stmt->fetchAll();

    // ── 6. Per-user stats for summary cards and achievements ──────────────────
    $stmt = $pdo->prepare("
        SELECT total_workouts, total_exercises, total_time, current_streak, last_workout_date
        FROM user_stats
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch() ?: [
        'total_workouts'    => 0,
        'total_exercises'   => 0,
        'total_time'        => 0,
        'current_streak'    => 0,
        'last_workout_date' => null,
    ];

    // ── 7. This-week summary ──────────────────────────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*)              AS week_workouts,
            COALESCE(SUM(duration), 0) AS week_time
        FROM workout_history
        WHERE user_id = ?
          AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ");
    $stmt->execute([$userId]);
    $weekSummary = $stmt->fetch();

    echo json_encode([
        'success'         => true,
        'daily_data'      => $dailyData,
        'session_data'    => $sessionData,
        'difficulty_dist' => $difficultyDist,
        'workout_dist'    => $workoutDist,
        'personal_records'=> $personalRecords,
        'stats'           => $stats,
        'week_summary'    => $weekSummary,
        'range_days'      => $days,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load progress data: ' . $e->getMessage()]);
}
