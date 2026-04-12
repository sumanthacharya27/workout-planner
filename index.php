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
require_once 'config/db.php'; // loads isAdmin() helper
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Workout Planner</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">💪 GymPlanner</div>
            <ul class="nav-menu">
                <li><a href="#" class="nav-link active" data-page="dashboard">Dashboard</a></li>
                <li><a href="#" class="nav-link" data-page="workouts">Workouts</a></li>
                <li><a href="#" class="nav-link" data-page="custom">Create</a></li>
                <li><a href="#" class="nav-link" data-page="history">History</a></li>
                <li><a href="#" class="nav-link" data-page="progress">Progress</a></li>
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
        <section id="dashboard" class="page-section active">
            <h1>Welcome to GymPlanner</h1>
            <p class="subtitle">Your personal workout companion</p>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div class="stat-number" id="totalWorkouts">0</div>
                    <div class="stat-label">Total Workouts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-number" id="currentStreak">0</div>
                    <div class="stat-label">Day Streak</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-number" id="totalTime">0</div>
                    <div class="stat-label">Hours Trained</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💪</div>
                    <div class="stat-number" id="totalExercises">0</div>
                    <div class="stat-label">Exercises Done</div>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <button class="action-btn primary" data-page="workouts">
                        <span class="btn-icon">📋</span>
                        <span>Browse Workouts</span>
                    </button>
                    <button class="action-btn secondary" data-page="custom">
                        <span class="btn-icon">➕</span>
                        <span>Create Workout</span>
                    </button>
                    <button class="action-btn tertiary" data-page="history">
                        <span class="btn-icon">📊</span>
                        <span>View History</span>
                    </button>
                </div>
            </div>

            <div class="recent-workouts">
                <h2>Recent Workouts</h2>
                <div id="recentWorkoutsList" class="recent-list">
                    <p class="empty-state">No workouts yet. Start your first workout!</p>
                </div>
            </div>
        </section>

        <!-- Pre-Made Workouts Section -->
        <section id="workouts" class="page-section">
            <h1>Pre-Made Workouts</h1>
            <p class="subtitle">Choose from our curated workout plans</p>

            <div class="filter-bar">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="beginner">Beginner</button>
                <button class="filter-btn" data-filter="intermediate">Intermediate</button>
                <button class="filter-btn" data-filter="advanced">Advanced</button>
            </div>

            <div id="workoutsList" class="workouts-grid">
                <!-- Workouts will be dynamically loaded here -->
            </div>
        </section>

        <!-- Custom Workout Builder Section -->
        <section id="custom" class="page-section">
            <h1>Create Custom Workout</h1>
            <p class="subtitle">Build your own personalized routine</p>

            <div class="workout-form">
                <div class="form-group">
                    <label for="workoutName">Workout Name</label>
                    <input type="text" id="workoutName" placeholder="e.g., My Leg Day">
                </div>

                <div class="form-group">
                    <label for="workoutDescription">Description (Optional)</label>
                    <textarea id="workoutDescription" rows="3" placeholder="Add notes about this workout..."></textarea>
                </div>

                <div class="exercises-section">
                    <div class="section-header">
                        <h2>Exercises</h2>
                        <button class="add-exercise-btn" id="addExerciseBtn">+ Add Exercise</button>
                    </div>
                    <div id="exercisesList" class="exercises-list">
                        <!-- Exercises will be added here -->
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn-cancel" id="cancelWorkout">Cancel</button>
                    <button class="btn-save" id="saveWorkout">Save Workout</button>
                </div>
            </div>

            <div class="saved-workouts">
                <h2>Your Saved Workouts</h2>
                <div id="savedWorkoutsList" class="saved-list">
                    <p class="empty-state">No custom workouts yet. Create your first one!</p>
                </div>
            </div>
        </section>

        <!-- Admin Panel Section -->
        <?php if (isAdmin()): ?>
        <section id="admin" class="page-section">
            <h1>Admin Panel</h1>
            <p class="subtitle">Manage pre-made workouts and exercises</p>

            <div class="admin-tabs">
                <button class="admin-tab-btn active" data-tab="manage-workouts">Manage Workouts</button>
                <button class="admin-tab-btn" data-tab="create-workout">Create Pre-Made Workout</button>
            </div>

            <!-- Manage Workouts Tab -->
            <div id="manage-workouts" class="admin-tab-content active">
                <h2>Pre-Made Workouts</h2>
                <div id="adminWorkoutsList" class="admin-workouts-list">
                    <p class="empty-state">Loading workouts...</p>
                </div>
            </div>

            <!-- Create Workout Tab -->
            <div id="create-workout" class="admin-tab-content">
                <h2>Create Pre-Made Workout</h2>
                <div class="admin-form">
                    <div class="form-group">
                        <label for="adminWorkoutName">Workout Name</label>
                        <input type="text" id="adminWorkoutName" placeholder="e.g., Upper Body Strength">
                    </div>

                    <div class="form-group">
                        <label for="adminWorkoutDesc">Description</label>
                        <textarea id="adminWorkoutDesc" rows="3" placeholder="Describe this workout..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="adminDifficulty">Difficulty Level</label>
                        <select id="adminDifficulty">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div class="exercises-section">
                        <div class="section-header">
                            <h3>Exercises</h3>
                            <button class="add-exercise-btn" id="adminAddExerciseBtn">+ Add Exercise</button>
                        </div>
                        <div id="adminExercisesList" class="exercises-list">
                            <!-- Exercises will be added here -->
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn-cancel" id="adminCancelWorkout">Cancel</button>
                        <button class="btn-save" id="adminSaveWorkout">Save Workout</button>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Workout Execution Section -->
        <section id="execute" class="page-section">
            <div class="workout-header">
                <button class="btn-back" id="backToWorkouts">← Back</button>
                <h1 id="executeWorkoutName">Workout Name</h1>
            </div>

            <div class="workout-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="workoutProgressBar"></div>
                </div>
                <p class="progress-text"><span id="currentExerciseNum">0</span> / <span id="totalExercisesNum">0</span> exercises</p>
            </div>

            <div class="exercise-display" id="exerciseDisplay">
                <!-- Current exercise will be displayed here -->
            </div>

            <div class="workout-controls">
                <button class="btn-secondary" id="prevExercise">Previous</button>
                <button class="btn-primary" id="nextExercise">Next Exercise</button>
            </div>

            <div class="workout-actions">
                <button class="btn-danger" id="quitWorkout">Quit Workout</button>
                <button class="btn-success" id="completeWorkout" style="display: none;">Complete Workout</button>
            </div>
        </section>

        <!-- History Section -->
        <section id="history" class="page-section">
            <h1>Workout History</h1>
            <p class="subtitle">Track your fitness journey</p>

            <div class="filter-bar">
                <button class="filter-btn active" data-filter="all">All Time</button>
                <button class="filter-btn" data-filter="week">This Week</button>
                <button class="filter-btn" data-filter="month">This Month</button>
            </div>

            <div id="historyList" class="history-list">
                <p class="empty-state">No workout history yet. Complete your first workout!</p>
            </div>
        </section>

        <!-- Progress Section -->
        <section id="progress" class="page-section">
            <div class="progress-header">
                <div>
                    <h1>Your Progress</h1>
                    <p class="subtitle">Track your fitness journey</p>
                </div>
                <div class="progress-range-selector">
                    <button class="range-btn active" data-range="7">7 Days</button>
                    <button class="range-btn" data-range="30">30 Days</button>
                    <button class="range-btn" data-range="90">90 Days</button>
                    <button class="range-btn" data-range="365">1 Year</button>
                </div>
            </div>

            <!-- Summary stat cards -->
            <div class="progress-summary-cards">
                <div class="prog-stat-card">
                    <div class="prog-stat-icon">🏋️</div>
                    <div class="prog-stat-value" id="progTotalWorkouts">—</div>
                    <div class="prog-stat-label">Total Workouts</div>
                </div>
                <div class="prog-stat-card">
                    <div class="prog-stat-icon">⏱️</div>
                    <div class="prog-stat-value" id="progTotalTime">—</div>
                    <div class="prog-stat-label">Hours Trained</div>
                </div>
                <div class="prog-stat-card">
                    <div class="prog-stat-icon">🔥</div>
                    <div class="prog-stat-value" id="progStreak">—</div>
                    <div class="prog-stat-label">Day Streak</div>
                </div>
                <div class="prog-stat-card">
                    <div class="prog-stat-icon">📅</div>
                    <div class="prog-stat-value" id="progWeekCount">—</div>
                    <div class="prog-stat-label">This Week</div>
                </div>
            </div>

            <!-- Charts row 1: Line + Bar -->
            <div class="charts-row">
                <div class="chart-card wide">
                    <div class="chart-card-header">
                        <h3>📈 Workout Frequency</h3>
                        <span class="chart-subtitle" id="freqSubtitle">Workouts per day</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="frequencyChart"></canvas>
                    </div>
                    <div class="chart-empty" id="freqEmpty" style="display:none">
                        No workout data for this period yet. Start training!
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>⏳ Session Duration</h3>
                        <span class="chart-subtitle">Minutes per session</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="durationChart"></canvas>
                    </div>
                    <div class="chart-empty" id="durEmpty" style="display:none">
                        No session data yet.
                    </div>
                </div>
            </div>

            <!-- Charts row 2: Pie + Pie -->
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>🥧 Difficulty Split</h3>
                        <span class="chart-subtitle">By difficulty level</span>
                    </div>
                    <div class="chart-wrap chart-wrap-pie">
                        <canvas id="difficultyChart"></canvas>
                    </div>
                    <div class="chart-empty" id="diffEmpty" style="display:none">
                        No data yet.
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>🏅 Favourite Workouts</h3>
                        <span class="chart-subtitle">Most completed</span>
                    </div>
                    <div class="chart-wrap chart-wrap-pie">
                        <canvas id="workoutDistChart"></canvas>
                    </div>
                    <div class="chart-empty" id="distEmpty" style="display:none">
                        No data yet.
                    </div>
                </div>
            </div>

            <!-- Personal Records -->
            <div class="pr-section">
                <h2>🏆 Personal Records</h2>
                <p class="section-subtitle">Best weight lifted per exercise across all sessions</p>
                <div id="prsList" class="prs-grid">
                    <p class="empty-state">Complete weighted workouts to see your personal records!</p>
                </div>
            </div>

            <!-- Achievements -->
            <div class="achievements-section">
                <h2>⭐ Achievements</h2>
                <p class="section-subtitle">Milestones you've hit on your journey</p>
                <div id="achievementsList" class="achievements-grid">
                    <!-- Rendered by JS -->
                </div>
            </div>
        </section>

    </div>

    <!-- Modal for Exercise Details -->
    <div id="exerciseModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Exercise</h2>
            <div class="modal-form">
                <div class="form-group">
                    <label for="exerciseName">Exercise Name</label>
                    <input type="text" id="exerciseName" placeholder="e.g., Barbell Squat">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="exerciseSets">Sets</label>
                        <input type="number" id="exerciseSets" min="1" value="3">
                    </div>
                    <div class="form-group">
                        <label for="exerciseReps">Reps</label>
                        <input type="number" id="exerciseReps" min="1" value="10">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="exerciseWeight">Weight (kg)</label>
                        <input type="number" id="exerciseWeight" min="0" step="0.5" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label for="exerciseRest">Rest (sec)</label>
                        <input type="number" id="exerciseRest" min="0" value="60">
                    </div>
                </div>
                <div class="form-group">
                    <label for="exerciseNotes">Notes (Optional)</label>
                    <textarea id="exerciseNotes" rows="2" placeholder="Add any notes..."></textarea>
                </div>
                <button class="btn-primary" id="saveExercise">Add Exercise</button>
            </div>
        </div>
    </div>

    <!-- Edit Workout Modal -->
    <div id="editWorkoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Workout</h2>
            <div class="modal-form">
                <div class="form-group">
                    <label for="editWorkoutName">Workout Name</label>
                    <input type="text" id="editWorkoutName">
                </div>
                <div class="form-group">
                    <label for="editWorkoutDesc">Description</label>
                    <textarea id="editWorkoutDesc" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="editDifficulty">Difficulty</label>
                    <select id="editDifficulty">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div id="editExercisesList" class="exercises-list"></div>
                <div class="form-actions">
                    <button class="btn-cancel" id="cancelEditWorkout">Cancel</button>
                    <button class="btn-save" id="saveEditWorkout">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // PHP passes role securely to JS — never trust this for backend checks,
        // it's only used for showing/hiding UI elements.
        const IS_ADMIN = <?= json_encode($_SESSION['role'] === 'admin') ?>;
        const CURRENT_USER = <?= json_encode($_SESSION['username']) ?>;
        const USER_ID = <?= json_encode((int)$_SESSION['user_id']) ?>;
    </script>
    <script src="assets/app.js"></script>
</body>
</html>
