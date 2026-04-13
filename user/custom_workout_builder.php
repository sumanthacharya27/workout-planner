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
