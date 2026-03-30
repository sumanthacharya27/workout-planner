<?php
session_start();
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
    <div id="app"></div>
    
    <!-- Auth Modal -->
    <div id="authModal" class="modal hidden">
        <div class="modal-content auth-form">
            <div class="form-toggle">
                <button class="toggle-btn active" data-form="login">Login</button>
                <button class="toggle-btn" data-form="register">Register</button>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" class="form-section active">
                <h2>Welcome Back</h2>
                <input type="email" placeholder="Email" required>
                <input type="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary">Login</button>
                <p class="form-message"></p>
            </form>
            
            <!-- Register Form -->
            <form id="registerForm" class="form-section">
                <h2>Create Account</h2>
                <input type="text" placeholder="Full Name" required>
                <input type="email" placeholder="Email" required>
                <input type="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary">Register</button>
                <p class="form-message"></p>
            </form>
        </div>
    </div>
    
    <!-- Main App Container (hidden until logged in) -->
    <div id="mainApp" class="hidden">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div class="header-content">
                    <h1 class="logo">GymPlanner Pro</h1>
                    <nav class="nav">
                        <button class="nav-btn active" data-page="dashboard">Dashboard</button>
                        <button class="nav-btn" data-page="library">Exercises</button>
                        <button class="nav-btn" data-page="builder">Build Workout</button>
                        <button class="nav-btn" data-page="workouts">My Workouts</button>
                    </nav>
                    <div class="user-menu">
                        <span id="userName"></span>
                        <button id="logoutBtn" class="btn btn-outline">Logout</button>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="container main-content">
            <!-- Dashboard Page -->
            <section id="dashboardPage" class="page active">
                <h2>Dashboard</h2>
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Workouts</div>
                        <div class="stat-value">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">This Week</div>
                        <div class="stat-value">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Exercises</div>
                        <div class="stat-value">0</div>
                    </div>
                </div>
            </section>
            
            <!-- Exercise Library Page -->
            <section id="libraryPage" class="page hidden">
                <h2>Exercise Library</h2>
                <div class="filters">
                    <select id="muscleFilter">
                        <option value="">All Muscle Groups</option>
                    </select>
                    <select id="difficultyFilter">
                        <option value="">All Difficulties</option>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                </div>
                <div id="exerciseList" class="exercise-list"></div>
            </section>
            
            <!-- Builder Page -->
            <section id="builderPage" class="page hidden">
                <h2>Build Your Workout</h2>
                <div class="builder-container">
                    <div class="builder-panel">
                        <h3>Workout Name</h3>
                        <input type="text" id="workoutName" placeholder="e.g., Upper Body Day" required>
                        <textarea id="workoutDesc" placeholder="Add a description (optional)"></textarea>
                        
                        <h3>Add Exercises</h3>
                        <select id="exerciseSelect">
                            <option value="">Select an exercise...</option>
                        </select>
                        <div class="exercise-config">
                            <input type="number" id="setsInput" placeholder="Sets" value="3" min="1">
                            <input type="number" id="repsInput" placeholder="Reps" value="10" min="1">
                            <input type="number" id="restInput" placeholder="Rest (sec)" value="60" min="0">
                            <button id="addExerciseBtn" class="btn btn-secondary">Add</button>
                        </div>
                        
                        <button id="saveWorkoutBtn" class="btn btn-primary">Save Workout</button>
                    </div>
                    
                    <div class="builder-preview">
                        <h3>Preview</h3>
                        <div id="workoutPreview" class="preview-list"></div>
                    </div>
                </div>
            </section>
            
            <!-- My Workouts Page -->
            <section id="workoutsPage" class="page hidden">
                <h2>My Workouts</h2>
                <div id="workoutsList" class="workouts-list"></div>
            </section>
        </main>
    </div>
    
    <script src="public/app.js"></script>
</body>
</html>
