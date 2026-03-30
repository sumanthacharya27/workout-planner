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
            
            <!-- Workout Templates Page -->
            <section id="templatesPage" class="page hidden">
                <h2>Workout Templates</h2>
                <p class="page-description">Choose from professionally designed workout routines to get started quickly.</p>

                <div id="templateAdminPanel" class="admin-panel hidden">
                    <h3>Admin: Add New Template</h3>
                    <div class="admin-grid">
                        <input type="hidden" id="adminTemplateId" />
                        <input id="adminTemplateName" placeholder="Template name" />
                        <input id="adminTemplateCategory" placeholder="Category" />
                        <input id="adminTemplateDifficulty" placeholder="Difficulty" />
                        <input type="number" id="adminTemplateDuration" placeholder="Duration (min)" min="5" />
                        <textarea id="adminTemplateDescription" placeholder="Description"></textarea>
                        <button id="adminCreateTemplateBtn" class="btn btn-primary">Create Template</button>
                    </div>
                    <p class="admin-hint">Template exercises are added through the exercise page, then assigned by template update using API.</p>
                </div>

                <div id="templatesList" class="templates-list"></div>
            </section>
            
            <!-- Exercise Library Page -->
            <section id="libraryPage" class="page hidden">
                <h2>Exercise Library</h2>
                <div id="exerciseAdminPanel" class="admin-panel hidden">
                    <h3>Admin: Add / Update Exercise</h3>
                    <div class="admin-grid">
                        <input type="hidden" id="adminExerciseId" />
                        <input id="adminExerciseName" placeholder="Exercise name" />
                        <input id="adminExerciseGroup" placeholder="Muscle group" />
                        <input id="adminExerciseDifficulty" placeholder="Difficulty" />
                        <input id="adminExerciseInstructions" placeholder="Instructions" />
                        <button id="adminCreateExerciseBtn" class="btn btn-primary">Create Exercise</button>
                    </div>
                </div>
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
            
            <!-- Workout Execution Page -->
            <section id="executionPage" class="page hidden">
                <h2>Workout Execution</h2>
                <div class="execution-header">
                    <h3 id="executionWorkoutName">Workout Name</h3>
                    <p id="executionWorkoutDesc">Workout description</p>
                    <div class="execution-progress">
                        <div class="progress-bar">
                            <div id="progressFill" class="progress-fill"></div>
                        </div>
                        <span id="progressText">0/0 exercises</span>
                    </div>
                </div>
                <div id="executionTimer" class="execution-timer">00:00</div>
                
                <div id="currentExercise" class="current-exercise">
                    <h3 id="currentExerciseName">Exercise Name</h3>
                    <div class="exercise-details">
                        <span id="currentExerciseSets">3 sets</span> × 
                        <span id="currentExerciseReps">10 reps</span> • 
                        <span id="currentExerciseRest">60s rest</span>
                    </div>
                    
                    <div class="set-tracker">
                        <h4>Sets</h4>
                        <div id="setList" class="set-list"></div>
                    </div>
                    
                    <div class="rep-counter">
                        <h4>Current Set Reps</h4>
                        <div class="rep-controls">
                            <button id="repMinus" class="rep-btn">-</button>
                            <span id="repCount">0</span>
                            <button id="repPlus" class="rep-btn">+</button>
                        </div>
                        <button id="completeSetBtn" class="btn btn-secondary">Complete Set</button>
                    </div>
                    
                    <div class="exercise-notes">
                        <textarea id="exerciseNotes" placeholder="Add notes for this exercise..."></textarea>
                    </div>
                </div>
                
                <div id="executionSteps" class="execution-steps"></div>
                <div class="execution-controls">
                    <button id="prevExerciseBtn" class="btn btn-outline">Previous</button>
                    <button id="nextExerciseBtn" class="btn btn-outline">Next</button>
                    <button id="completeWorkoutBtn" class="btn btn-primary">Complete Workout</button>
                    <button id="startRestBtn" class="btn btn-secondary">Start Rest</button>
                </div>
                <div id="restCountdown" class="rest-countdown">Rest: 0s</div>
            </section>
            
            <!-- Workout History Page -->
            <section id="historyPage" class="page hidden">
                <h2>Workout History</h2>
                <div id="historyList" class="history-list"></div>
            </section>
        </main>
    </div>
    
    <script src="public/app.js"></script>
</body>
</html>
