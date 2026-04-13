        <!-- Admin Panel Section -->
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
