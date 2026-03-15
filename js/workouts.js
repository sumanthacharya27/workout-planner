// ========================================
// WORKOUTS MODULE
// ========================================

let currentExercises = [];

// Create workout
async function createWorkout(workoutData) {
    const formData = new FormData();
    formData.append('plan_name', workoutData.name);
    formData.append('difficulty', workoutData.difficulty);
    formData.append('description', workoutData.description);
    formData.append('exercises', JSON.stringify(workoutData.exercises));
    
    const response = await fetch(ENDPOINTS.WORKOUTS + '?action=create', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Get all workouts
async function getAllWorkouts() {
    const response = await fetch(ENDPOINTS.WORKOUTS + '?action=getAll');
    return await response.json();
}

// Get single workout
async function getWorkout(planId) {
    const response = await fetch(ENDPOINTS.WORKOUTS + '?action=get&plan_id=' + planId);
    return await response.json();
}

// Delete workout
async function deleteWorkout(planId) {
    const formData = new FormData();
    formData.append('plan_id', planId);
    
    const response = await fetch(ENDPOINTS.WORKOUTS + '?action=delete', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Add exercise to list
function addExercise(exercise) {
    currentExercises.push(exercise);
    renderExercises();
}

// Remove exercise from list
function removeExercise(index) {
    currentExercises.splice(index, 1);
    renderExercises();
}

// Render exercises list
function renderExercises() {
    const container = document.getElementById('exercisesList');
    
    if (!container) return;
    
    if (currentExercises.length === 0) {
        container.innerHTML = '<p class="empty-state">No exercises added yet.</p>';
        return;
    }
    
    container.innerHTML = currentExercises.map((ex, index) => `
        <div class="exercise-item">
            <div class="exercise-info">
                <div class="exercise-name">${ex.name}</div>
                <div class="exercise-details">
                    ${ex.sets} sets × ${ex.reps} reps
                    ${ex.weight > 0 ? `• ${ex.weight}kg` : ''}
                    • ${ex.rest}s rest
                    ${ex.notes ? `• ${ex.notes}` : ''}
                </div>
            </div>
            <button class="btn-delete" onclick="removeExercise(${index})">🗑️</button>
        </div>
    `).join('');
}

// Display workouts list
function displayWorkouts(workouts) {
    const container = document.getElementById('workoutsList');
    
    if (!container) return;
    
    if (workouts.length === 0) {
        container.innerHTML = '<p class="empty-state">No workouts yet. Create your first one!</p>';
        return;
    }
    
    container.innerHTML = workouts.map(workout => `
        <div class="workout-card">
            <div class="workout-header">
                <h3>${workout.plan_name}</h3>
                <span class="badge badge-${workout.difficulty}">${workout.difficulty}</span>
            </div>
            <p class="workout-description">${workout.description || 'No description'}</p>
            <div class="workout-info">
                <span>📝 ${workout.exercise_count} exercises</span>
            </div>
            <div class="workout-actions">
                <button class="btn-primary btn-sm" onclick="viewWorkout(${workout.plan_id})">View</button>
                <button class="btn-danger btn-sm" onclick="confirmDelete(${workout.plan_id})">Delete</button>
            </div>
        </div>
    `).join('');
}

// View workout details
async function viewWorkout(planId) {
    const result = await getWorkout(planId);
    
    if (result.success) {
        const workout = result.data;
        const exercisesList = workout.exercises.map(ex => 
            `• ${ex.exercise_name}: ${ex.sets}×${ex.reps}${ex.weight > 0 ? ` @ ${ex.weight}kg` : ''}`
        ).join('\n');
        
        alert(`${workout.plan_name}\n\nDifficulty: ${workout.difficulty}\n${workout.description}\n\nExercises:\n${exercisesList}`);
    } else {
        alert(result.message);
    }
}

// Confirm delete
async function confirmDelete(planId) {
    if (confirm('Are you sure you want to delete this workout?')) {
        const result = await deleteWorkout(planId);
        
        if (result.success) {
            alert('Workout deleted!');
            loadAllWorkouts();
        } else {
            alert(result.message);
        }
    }
}

// Load all workouts
async function loadAllWorkouts() {
    const result = await getAllWorkouts();
    
    if (result.success) {
        displayWorkouts(result.data);
    }
}