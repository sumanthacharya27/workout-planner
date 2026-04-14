<?php
// ============================================
// DELETE WORKOUT API
// ============================================
// api/delete_workout.php
// Admin can delete any workout; users can only delete their own custom workouts
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);


header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once '../config/db.php';

// ── 1. Method guard ───────────────────────────────────────────────────────────
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── 2. Authentication ─────────────────────────────────────────────────────────
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // ── 3. CSRF validation ────────────────────────────────────────────────────
    /*$clientToken = $data['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (empty($sessionToken) || !hash_equals($sessionToken, $clientToken)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid or missing CSRF token']);
        exit;
    }*/

    // ── 4. Input validation ───────────────────────────────────────────────────
    if (empty($data['workoutId']) || !is_numeric($data['workoutId'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'A valid Workout ID is required']);
        exit;
    }

    $workoutId = (int) $data['workoutId'];

    // ── 5. Fetch workout ──────────────────────────────────────────────────────
    $check = $pdo->prepare("SELECT id, is_custom, created_by FROM workouts WHERE id = ?");
    $check->execute([$workoutId]);
    $workout = $check->fetch();

    if (!$workout) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Workout not found']);
        exit;
    }

    // ── 6. Authorization ──────────────────────────────────────────────────────
    if (!isAdmin()) {
        $createdBy = $workout['created_by'];

        // Guard against NULL created_by
        if (
            is_null($createdBy)                          ||
            !$workout['is_custom']                       ||
            (int) $createdBy !== (int) $_SESSION['user_id']
        ) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You can only delete your own custom workouts']);
            exit;
        }
    }

 // ── 7. Delete & verify (FINAL CLEAN VERSION) ────────────────────────────────
$pdo->beginTransaction();

try {
    // Step 1: Delete related history
    $deleteHistory = $pdo->prepare("DELETE FROM workout_history WHERE workout_id = ?");
    $deleteHistory->execute([$workoutId]);

    // Step 2: Delete workout
    $stmt = $pdo->prepare("DELETE FROM workouts WHERE id = ?");
    $stmt->execute([$workoutId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Workout not found or already deleted');
    }

    $pdo->commit();

    ob_clean(); // ✅ remove any accidental output
    echo json_encode([
        'success' => true,
        'message' => 'Workout and related history deleted successfully'
    ]);
    exit; // ✅ CRITICAL

} catch (Exception $e) {
    $pdo->rollBack();

    error_log('[delete_workout] Transaction failed: ' . $e->getMessage());

    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete workout'
    ]);
    exit; // ✅ CRITICAL
}

    // Guard against race condition where another request deleted it first
   /* if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Workout could not be deleted — it may have already been removed']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Workout deleted successfully'
    ]); */

} catch (PDOException $e) {
    // Log internally — never expose raw DB errors to the client
    error_log('[delete_workout] PDOException: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to delete workout. Please try again later.'
    ]);
}