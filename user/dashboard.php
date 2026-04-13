<!-- Dashboard Section -->
<section id="dashboard" class="page-section active">
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Gym-Goer'); ?>! 👋</h1>
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
