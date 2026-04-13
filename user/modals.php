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
