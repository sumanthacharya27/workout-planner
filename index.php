<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymPlanner Pro</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
   
    
<?php if (!$isLoggedIn): ?>

    <?php include 'modals/auth.php'; ?>

<?php else: ?>

    <div id="mainApp">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div class="header-content">
                    <h1 class="logo">GymPlanner Pro</h1>
                    <nav class="nav">
                        <button class="nav-btn active" data-page="dashboard">Dashboard</button>
                        <button class="nav-btn" data-page="templates">Templates</button>
                        <button class="nav-btn" data-page="library">Exercises</button>
                        <button class="nav-btn" data-page="builder">Build Workout</button>
                        <button class="nav-btn" data-page="workouts">My Workouts</button>
                        <button class="nav-btn" data-page="history">History</button>                    </nav>
                    <div class="user-menu">
                        <span id="userName"></span>
                        <button id="logoutBtn" class="btn btn-outline">Logout</button>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
         <main class="container main-content">
            <?php include 'pages/dashboard.php'; ?>
            <?php include 'pages/templates.php'; ?>
            <?php include 'pages/library.php'; ?>
            <?php include 'pages\builder.php'; ?>
            <?php include 'pages/workouts.php'; ?>
            <?php include 'pages/execution.php'; ?>
            <?php include 'pages/history.php'; ?>
        </main>
    </div>

<?php endif; ?>
    
    <script src="public/app.js"></script>
</body>
</html>
