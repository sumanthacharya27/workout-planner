<?php
// ============================================
// SESSION CHECK
// ============================================
// Verify user is logged in

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Workout Planner</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script>
        window.APP_CONFIG = {
            role: '<?php echo strtolower(trim($_SESSION['role'] ?? 'user')); ?>',
            userId: <?php echo (int)($_SESSION['user_id'] ?? 0); ?>,
            username: '<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>'
        };
    </script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">  MuscleMap</div>
            <ul class="nav-menu">
                <li><a href="#" class="nav-link active" data-page="dashboard">Dashboard</a></li>
                <li><a href="#" class="nav-link" data-page="workouts">Workouts</a></li>
                <li><a href="#" class="nav-link" data-page="custom">Create</a></li>
                <li><a href="#" class="nav-link" data-page="history">History</a></li>
                <li><a href="#" class="nav-link" data-page="progress">Progress</a></li>
                <li><a href="#" class="nav-link" data-page="calculator">Calculator</a></li>
                <?php if (isAdmin()): ?>
                <li><a href="#" class="nav-link" data-page="admin">Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-link logout-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        
        <!-- Dashboard Section -->
        <?php include 'user/dashboard.php'; ?>

        <!-- Pre-Made Workouts Section -->
        <?php include 'user/workouts.php'; ?>

        <!-- Custom Workout Builder Section -->
        <?php include 'user/custom_workout_builder.php'; ?>

        <!-- Admin Panel Section -->
        <?php if (isAdmin()): ?>
            <?php include 'admin/admin_panel.php'; ?>
        <?php endif; ?>

        <!-- Workout Execution Section -->
        <?php include 'user/workout_execution.php'; ?>

        <!-- History Section -->
        <?php include 'user/history.php'; ?>

        <!-- Progress Section -->
        <?php include 'user/progress_section.php'; ?>

        <!-- Calculator Section -->
        <?php include 'user/calculator.php'; ?>

    </div>

    <!-- UI Modals -->
    <?php include 'user/modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/app.js"></script>
</body>
</html>
