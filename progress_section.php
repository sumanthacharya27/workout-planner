        <!-- Progress Section -->
        <section id="progress" class="page-section">
            <h1>Your Progress</h1>
            <p class="subtitle">Visualize your achievements with real workout analytics</p>

            <div class="filter-bar">
                <button class="filter-btn active progress-range-btn" data-range="7">7 Days</button>
                <button class="filter-btn progress-range-btn" data-range="30">30 Days</button>
                <button class="filter-btn progress-range-btn" data-range="90">90 Days</button>
                <button class="filter-btn progress-range-btn" data-range="365">1 Year</button>
            </div>

            <div class="dashboard-stats" id="progressSummaryCards">
                <div class="stat-card">
                    <div class="stat-icon">🏋️</div>
                    <div class="stat-number" id="progressTotalWorkouts">0</div>
                    <div class="stat-label">Total Workouts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div class="stat-number" id="progressCurrentStreak">0</div>
                    <div class="stat-label">Current Streak</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-number" id="progressWeekTime">0m</div>
                    <div class="stat-label">Last 7 Days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-number" id="progressRangeWorkouts">0</div>
                    <div class="stat-label">In Selected Range</div>
                </div>
            </div>

            <div class="progress-stats">
                <div class="stat-card-large">
                    <h3>Workout Frequency</h3>
                    <canvas id="frequencyChart"></canvas>
                </div>
                <div class="stat-card-large">
                    <h3>Workout Type Distribution</h3>
                    <canvas id="exerciseChart"></canvas>
                </div>
                <div class="stat-card-large">
                    <h3>Session Duration Trend</h3>
                    <canvas id="durationChart"></canvas>
                </div>
                <div class="stat-card-large">
                    <h3>Difficulty Distribution</h3>
                    <canvas id="difficultyChart"></canvas>
                </div>
            </div>

            <div class="personal-records">
                <h2>Personal Records</h2>
                <div id="prsList" class="prs-list">
                    <p class="empty-state">Complete workouts to track your personal records!</p>
                </div>
            </div>

            <div class="achievements">
                <h2>Achievements & Milestones</h2>
                <div id="achievementsList" class="achievements-grid"></div>
            </div>
        </section>
