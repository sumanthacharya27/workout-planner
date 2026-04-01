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