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