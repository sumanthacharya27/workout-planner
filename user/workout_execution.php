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
