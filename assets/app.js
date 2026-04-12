// ============================================
// GYM PLANNER - FULL STACK VERSION
// ============================================
// Uses PHP backend and MySQL database

// ============================================
// CONFIGURATION
// ============================================
const API_BASE = '/gym_plan';  // App lives at localhost/gym_plan/

// ============================================
// STATE MANAGEMENT
// ============================================
let currentWorkout = null;
let currentExerciseIndex = 0;
let restTimerInterval = null;
let workoutStartTime = null;
let allWorkouts = [];
let userStats = {
    total_workouts: 0,
    total_exercises: 0,
    total_time: 0,
    current_streak: 0
};

// ============================================
// API FUNCTIONS
// ============================================

/**
 * Fetch all workouts from backend
 */
async function fetchWorkouts() {
    try {
        const response = await fetch(`${API_BASE}/api/get_workouts.php`);
        const result = await response.json();
        
        if (result.success) {
            allWorkouts = result.data;
            return allWorkouts;
        } else {
            showError('Failed to load workouts');
            return [];
        }
    } catch (error) {
        console.error('Error fetching workouts:', error);
        showError('Error loading workouts');
        return [];
    }
}

/**
 * Save custom workout to backend
 */
async function saveCustomWorkout(name, description, exercises) {
    try {
        const response = await fetch(`${API_BASE}/api/save_workout.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name,
                description,
                exercises
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Workout saved successfully!');
            return result.workoutId;
        } else {
            showError(result.error || 'Failed to save workout');
            return null;
        }
    } catch (error) {
        console.error('Error saving workout:', error);
        showError('Error saving workout');
        return null;
    }
}

/**
 * Save workout completion to history
 */
async function saveWorkoutHistory(workoutId, duration, notes = '') {
    try {
        const response = await fetch(`${API_BASE}/api/save_history.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                workoutId,
                duration,
                notes
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Refresh stats after saving history
            await fetchUserStats();
            return true;
        } else {
            showError(result.error || 'Failed to save history');
            return false;
        }
    } catch (error) {
        console.error('Error saving history:', error);
        showError('Error saving workout history');
        return false;
    }
}

/**
 * Fetch workout history
 */
async function fetchWorkoutHistory(filter = 'all') {
    try {
        const response = await fetch(`${API_BASE}/api/get_history.php?filter=${filter}`);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            showError('Failed to load history');
            return [];
        }
    } catch (error) {
        console.error('Error fetching history:', error);
        showError('Error loading history');
        return [];
    }
}

/**
 * Fetch user statistics
 */
async function fetchUserStats() {
    try {
        const response = await fetch(`${API_BASE}/api/get_stats.php`);
        const result = await response.json();
        
        if (result.success) {
            userStats = result.data;
            updateDashboardStats();
            return userStats;
        } else {
            showError('Failed to load stats');
            return userStats;
        }
    } catch (error) {
        console.error('Error fetching stats:', error);
        showError('Error loading stats');
        return userStats;
    }
}

/**
 * Admin: Save pre-made workout
 */
async function adminSaveWorkout(name, description, difficulty, exercises) {
    try {
        const response = await fetch(`${API_BASE}/api/admin_save_workout.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name,
                description,
                difficulty,
                exercises
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Pre-made workout created successfully!');
            return result.workoutId;
        } else {
            showError(result.error || 'Failed to create workout');
            return null;
        }
    } catch (error) {
        console.error('Error creating workout:', error);
        showError('Error creating workout');
        return null;
    }
}

/**
 * Delete workout (admin only)
 */
async function deleteWorkout(workoutId) {
    if (!confirm('Are you sure you want to delete this workout?')) {
        return false;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/delete_workout.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ workoutId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Workout deleted successfully!');
            return true;
        } else {
            showError(result.error || 'Failed to delete workout');
            return false;
        }
    } catch (error) {
        console.error('Error deleting workout:', error);
        showError('Error deleting workout');
        return false;
    }
}

/**
 * Update workout
 */
async function updateWorkout(workoutId, name, description, difficulty, exercises) {
    try {
        const response = await fetch(`${API_BASE}/api/update_workout.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                workoutId,
                name,
                description,
                difficulty,
                exercises
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Workout updated successfully!');
            return true;
        } else {
            showError(result.error || 'Failed to update workout');
            return false;
        }
    } catch (error) {
        console.error('Error updating workout:', error);
        showError('Error updating workout');
        return false;
    }
}

// ============================================
// UI UPDATE FUNCTIONS
// ============================================

/**
 * Update dashboard statistics display
 */
function updateDashboardStats() {
    document.getElementById('totalWorkouts').textContent = userStats.total_workouts || 0;
    document.getElementById('currentStreak').textContent = userStats.current_streak || 0;
    document.getElementById('totalTime').textContent = Math.round((userStats.total_time || 0) / 60) || 0;
    document.getElementById('totalExercises').textContent = userStats.total_exercises || 0;
}

/**
 * Render workouts list
 */
function renderWorkoutsList(workouts, filter = 'all') {
    const container = document.getElementById('workoutsList');
    
    let filtered = workouts.filter(w => !w.is_custom);
    if (filter !== 'all') {
        filtered = filtered.filter(w => w.difficulty === filter);
    }
    
    if (filtered.length === 0) {
        container.innerHTML = '<p class="empty-state">No workouts found</p>';
        return;
    }
    
    container.innerHTML = filtered.map(workout => `
        <div class="workout-card" data-workout-id="${workout.id}">
            <div class="difficulty-badge ${workout.difficulty}">${workout.difficulty}</div>
            <h3 class="workout-title">${workout.name}</h3>
            <p class="workout-description">${workout.description || ''}</p>
            <div class="workout-info">
                <span class="info-item">
                    <span class="info-icon">🏋️</span>
                    <span>${workout.exercises.length} exercises</span>
                </span>
            </div>
            <button class="btn-start-workout" onclick="startWorkout(${workout.id})">Start Workout</button>
        </div>
    `).join('');
}

/**
 * Render saved custom workouts
 */
function renderSavedWorkouts(workouts) {
    const container = document.getElementById('savedWorkoutsList');
    
    let custom = workouts.filter(w => w.is_custom);
    
    if (custom.length === 0) {
        container.innerHTML = '<p class="empty-state">No custom workouts yet. Create your first one!</p>';
        return;
    }
    
    container.innerHTML = custom.map(workout => `
        <div class="saved-workout-item">
            <h3>${workout.name}</h3>
            <p>${workout.description || 'No description'}</p>
            <p class="workout-meta">${workout.exercises.length} exercises</p>
            <div class="saved-workout-actions">
                <button class="btn-small btn-primary" onclick="startWorkout(${workout.id})">Start</button>
                <button class="btn-small btn-secondary" onclick="editCustomWorkout(${workout.id})">Edit</button>
                <button class="btn-small btn-danger" onclick="deleteWorkout(${workout.id})">Delete</button>
            </div>
        </div>
    `).join('');
}

/**
 * Render admin workouts list
 */
function renderAdminWorkoutsList(workouts) {
    const container = document.getElementById('adminWorkoutsList');
    
    let premade = workouts.filter(w => !w.is_custom);
    
    if (premade.length === 0) {
        container.innerHTML = '<p class="empty-state">No pre-made workouts yet</p>';
        return;
    }
    
    container.innerHTML = premade.map(workout => `
        <div class="admin-workout-item">
            <div class="admin-workout-header">
                <h3>${workout.name}</h3>
                <span class="difficulty-badge ${workout.difficulty}">${workout.difficulty}</span>
            </div>
            <p class="admin-workout-desc">${workout.description || 'No description'}</p>
            <div class="admin-workout-exercises">
                <strong>Exercises (${workout.exercises.length}):</strong>
                <ul>
                    ${workout.exercises.map(ex => `<li>${ex.name} - ${ex.sets}x${ex.reps}</li>`).join('')}
                </ul>
            </div>
            <div class="admin-workout-actions">
                <button class="btn-small btn-primary" onclick="editAdminWorkout(${workout.id})">Edit</button>
                <button class="btn-small btn-danger" onclick="deleteWorkout(${workout.id})">Delete</button>
            </div>
        </div>
    `).join('');
}

/**
 * Render workout history
 */
async function renderWorkoutHistory(filter = 'all') {
    const container = document.getElementById('historyList');
    const history = await fetchWorkoutHistory(filter);
    
    if (history.length === 0) {
        container.innerHTML = '<p class="empty-state">No workout history yet</p>';
        return;
    }
    
    container.innerHTML = history.map(item => `
        <div class="history-item">
            <div class="history-header">
                <h3 class="history-title">${item.workout_name}</h3>
                <span class="history-date">${new Date(item.completed_at).toLocaleDateString()}</span>
            </div>
            <p class="history-exercises">${item.exercise_count} exercises</p>
            <div class="history-stats">
                <span>Duration: ${Math.round(item.duration / 60)} mins</span>
            </div>
        </div>
    `).join('');
}

/**
 * Show success message
 */
function showSuccess(message) {
    const alert = document.createElement('div');
    alert.className = 'success-alert';
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

/**
 * Show error message
 */
function showError(message) {
    const alert = document.createElement('div');
    alert.className = 'error-alert';
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

// ============================================
// WORKOUT EXECUTION FUNCTIONS
// ============================================

/**
 * Start a workout
 */
function startWorkout(workoutId) {
    const workout = allWorkouts.find(w => w.id === workoutId);
    
    if (!workout || !workout.exercises || workout.exercises.length === 0) {
        showError('Workout not found or has no exercises');
        return;
    }
    
    currentWorkout = workout;
    currentExerciseIndex = 0;
    workoutStartTime = Date.now();
    
    showPage('execute');
    document.getElementById('executeWorkoutName').textContent = workout.name;
    displayCurrentExercise();
}

/**
 * Display current exercise
 */
function displayCurrentExercise() {
    if (!currentWorkout) return;
    
    const exercise = currentWorkout.exercises[currentExerciseIndex];
    const totalExercises = currentWorkout.exercises.length;
    
    // Update progress
    document.getElementById('currentExerciseNum').textContent = currentExerciseIndex + 1;
    document.getElementById('totalExercisesNum').textContent = totalExercises;
    
    const progressPercent = ((currentExerciseIndex + 1) / totalExercises) * 100;
    document.getElementById('workoutProgressBar').style.width = progressPercent + '%';
    
    // Display exercise
    const display = document.getElementById('exerciseDisplay');
    display.innerHTML = `
        <div class="exercise-card">
            <h2>${exercise.name}</h2>
            <div class="exercise-details">
                <div class="detail-group">
                    <span class="detail-label">Sets</span>
                    <span class="detail-value">${exercise.sets}</span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Reps</span>
                    <span class="detail-value">${exercise.reps}</span>
                </div>
                ${exercise.weight > 0 ? `
                    <div class="detail-group">
                        <span class="detail-label">Weight</span>
                        <span class="detail-value">${exercise.weight} kg</span>
                    </div>
                ` : ''}
                <div class="detail-group">
                    <span class="detail-label">Rest</span>
                    <span class="detail-value">${exercise.rest_time}s</span>
                </div>
            </div>
            ${exercise.notes ? `<p class="exercise-notes"><strong>Notes:</strong> ${exercise.notes}</p>` : ''}
            <div class="sets-tracker">
                <p class="tracker-label">Mark sets as complete:</p>
                <div class="sets-grid" id="setsGrid">
                    ${Array(exercise.sets).fill().map((_, i) => `
                        <label class="set-checkbox">
                            <input type="checkbox" data-set="${i + 1}">
                            <span>Set ${i + 1}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    // Update button visibility
    document.getElementById('prevExercise').style.display = currentExerciseIndex > 0 ? 'block' : 'none';
    document.getElementById('nextExercise').style.display = currentExerciseIndex < totalExercises - 1 ? 'block' : 'none';
    document.getElementById('completeWorkout').style.display = currentExerciseIndex === totalExercises - 1 ? 'block' : 'none';
}

/**
 * Navigate to next exercise
 */
function nextExercise() {
    if (currentWorkout && currentExerciseIndex < currentWorkout.exercises.length - 1) {
        currentExerciseIndex++;
        displayCurrentExercise();
    }
}

/**
 * Navigate to previous exercise
 */
function previousExercise() {
    if (currentExerciseIndex > 0) {
        currentExerciseIndex--;
        displayCurrentExercise();
    }
}

/**
 * Complete workout
 */
async function completeWorkout() {
    if (!currentWorkout || !workoutStartTime) {
        showError('No active workout');
        return;
    }
    
    const duration = Math.round((Date.now() - workoutStartTime) / 1000);
    
    // Save to history
    const success = await saveWorkoutHistory(currentWorkout.id, duration);
    
    if (success) {
        showSuccess('Workout completed! Great job! 💪');
        setTimeout(() => {
            showPage('dashboard');
            currentWorkout = null;
            currentExerciseIndex = 0;
        }, 1500);
    }
}

/**
 * Quit workout
 */
function quitWorkout() {
    if (confirm('Are you sure you want to quit this workout?')) {
        currentWorkout = null;
        currentExerciseIndex = 0;
        workoutStartTime = null;
        showPage('dashboard');
    }
}

// ============================================
// PAGE NAVIGATION
// ============================================

/**
 * Show specific page
 */
function showPage(pageId) {
    document.querySelectorAll('.page-section').forEach(section => {
        section.classList.remove('active');
    });
    
    document.getElementById(pageId).classList.add('active');
    
    // Update nav links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-page') === pageId) {
            link.classList.add('active');
        }
    });
    
    // Load data when showing certain pages
    if (pageId === 'history') {
        renderWorkoutHistory('all');
    } else if (pageId === 'admin' && document.getElementById('admin')) {
        renderAdminWorkoutsList(allWorkouts);
    }
}

// ============================================
// CUSTOM WORKOUT MANAGEMENT
// ============================================

let customExercises = [];

/**
 * Add exercise to custom workout form
 */
function addExerciseToCustom() {
    const modal = document.getElementById('exerciseModal');
    modal.style.display = 'block';
    
    // Reset form
    document.getElementById('exerciseName').value = '';
    document.getElementById('exerciseSets').value = '3';
    document.getElementById('exerciseReps').value = '10';
    document.getElementById('exerciseWeight').value = '';
    document.getElementById('exerciseRest').value = '60';
    document.getElementById('exerciseNotes').value = '';
    
    // Save exercise handler
    document.getElementById('saveExercise').onclick = function() {
        const name = document.getElementById('exerciseName').value;
        const sets = document.getElementById('exerciseSets').value;
        const reps = document.getElementById('exerciseReps').value;
        const weight = document.getElementById('exerciseWeight').value;
        const rest = document.getElementById('exerciseRest').value;
        const notes = document.getElementById('exerciseNotes').value;
        
        if (!name) {
            showError('Exercise name is required');
            return;
        }
        
        customExercises.push({
            name,
            sets: parseInt(sets),
            reps: parseInt(reps),
            weight: parseFloat(weight) || 0,
            rest: parseInt(rest),
            notes
        });
        
        renderCustomExercises();
        modal.style.display = 'none';
        showSuccess('Exercise added');
    };
}

/**
 * Render custom exercises list
 */
function renderCustomExercises() {
    const container = document.getElementById('exercisesList');
    
    if (customExercises.length === 0) {
        container.innerHTML = '<p class="empty-state">No exercises added yet</p>';
        return;
    }
    
    container.innerHTML = customExercises.map((ex, index) => `
        <div class="exercise-item">
            <div class="exercise-item-info">
                <h4>${ex.name}</h4>
                <p>${ex.sets}x${ex.reps}${ex.weight > 0 ? ` @ ${ex.weight}kg` : ''} - Rest: ${ex.rest}s</p>
                ${ex.notes ? `<p class="exercise-notes">${ex.notes}</p>` : ''}
            </div>
            <button class="btn-remove" onclick="removeExercise(${index})">Remove</button>
        </div>
    `).join('');
}

/**
 * Remove exercise from custom workout
 */
function removeExercise(index) {
    customExercises.splice(index, 1);
    renderCustomExercises();
}

/**
 * Save custom workout
 */
async function saveCustomWorkoutForm() {
    const name = document.getElementById('workoutName').value;
    const description = document.getElementById('workoutDescription').value;
    
    if (!name) {
        showError('Workout name is required');
        return;
    }
    
    if (customExercises.length === 0) {
        showError('Please add at least one exercise');
        return;
    }
    
    const workoutId = await saveCustomWorkout(name, description, customExercises);
    
    if (workoutId) {
        // Reset form
        document.getElementById('workoutName').value = '';
        document.getElementById('workoutDescription').value = '';
        customExercises = [];
        renderCustomExercises();
        
        // Reload workouts
        await loadWorkouts();
        renderSavedWorkouts(allWorkouts);
    }
}

// ============================================
// ADMIN FUNCTIONS
// ============================================

let adminExercises = [];

/**
 * Add exercise to admin workout
 */
function addExerciseToAdmin() {
    const modal = document.getElementById('exerciseModal');
    modal.style.display = 'block';
    
    // Reset form
    document.getElementById('exerciseName').value = '';
    document.getElementById('exerciseSets').value = '3';
    document.getElementById('exerciseReps').value = '10';
    document.getElementById('exerciseWeight').value = '';
    document.getElementById('exerciseRest').value = '60';
    document.getElementById('exerciseNotes').value = '';
    
    // Save exercise handler
    document.getElementById('saveExercise').onclick = function() {
        const name = document.getElementById('exerciseName').value;
        const sets = document.getElementById('exerciseSets').value;
        const reps = document.getElementById('exerciseReps').value;
        const weight = document.getElementById('exerciseWeight').value;
        const rest = document.getElementById('exerciseRest').value;
        const notes = document.getElementById('exerciseNotes').value;
        
        if (!name) {
            showError('Exercise name is required');
            return;
        }
        
        adminExercises.push({
            name,
            sets: parseInt(sets),
            reps: parseInt(reps),
            weight: parseFloat(weight) || 0,
            rest: parseInt(rest),
            notes
        });
        
        renderAdminExercises();
        modal.style.display = 'none';
        showSuccess('Exercise added');
    };
}

/**
 * Render admin exercises list
 */
function renderAdminExercises() {
    const container = document.getElementById('adminExercisesList');
    
    if (adminExercises.length === 0) {
        container.innerHTML = '<p class="empty-state">No exercises added yet</p>';
        return;
    }
    
    container.innerHTML = adminExercises.map((ex, index) => `
        <div class="exercise-item">
            <div class="exercise-item-info">
                <h4>${ex.name}</h4>
                <p>${ex.sets}x${ex.reps}${ex.weight > 0 ? ` @ ${ex.weight}kg` : ''} - Rest: ${ex.rest}s</p>
            </div>
            <button class="btn-remove" onclick="removeAdminExercise(${index})">Remove</button>
        </div>
    `).join('');
}

/**
 * Remove exercise from admin workout
 */
function removeAdminExercise(index) {
    adminExercises.splice(index, 1);
    renderAdminExercises();
}

/**
 * Save admin workout
 */
async function saveAdminWorkoutForm() {
    const name = document.getElementById('adminWorkoutName').value;
    const description = document.getElementById('adminWorkoutDesc').value;
    const difficulty = document.getElementById('adminDifficulty').value;
    
    if (!name) {
        showError('Workout name is required');
        return;
    }
    
    if (adminExercises.length === 0) {
        showError('Please add at least one exercise');
        return;
    }
    
    const workoutId = await adminSaveWorkout(name, description, difficulty, adminExercises);
    
    if (workoutId) {
        // Reset form
        document.getElementById('adminWorkoutName').value = '';
        document.getElementById('adminWorkoutDesc').value = '';
        adminExercises = [];
        renderAdminExercises();
        
        // Reload workouts
        await loadWorkouts();
        renderAdminWorkoutsList(allWorkouts);
    }
}

/**
 * Edit a custom workout (user-created)
 */
function editCustomWorkout(workoutId) {
    const workout = allWorkouts.find(w => w.id === workoutId);
    if (!workout) { showError('Workout not found'); return; }

    // Populate modal fields
    document.getElementById('editWorkoutName').value = workout.name;
    document.getElementById('editWorkoutDesc').value = workout.description || '';
    document.getElementById('editDifficulty').value = workout.difficulty || 'beginner';

    // Build editable exercises list inside the modal
    let editExercises = workout.exercises.map(ex => ({
        name: ex.name,
        sets: ex.sets,
        reps: ex.reps,
        weight: ex.weight,
        rest: ex.rest_time,
        notes: ex.notes || ''
    }));

    function renderEditList() {
        const container = document.getElementById('editExercisesList');
        if (editExercises.length === 0) {
            container.innerHTML = '<p class="empty-state">No exercises. Add some!</p>';
            return;
        }
        container.innerHTML = editExercises.map((ex, i) => `
            <div class="exercise-item">
                <div class="exercise-item-info">
                    <h4>${ex.name}</h4>
                    <p>${ex.sets}x${ex.reps}${ex.weight > 0 ? ` @ ${ex.weight}kg` : ''} - Rest: ${ex.rest}s</p>
                    ${ex.notes ? `<p class="exercise-notes">${ex.notes}</p>` : ''}
                </div>
                <button class="btn-remove" onclick="editExercises.splice(${i},1);renderEditList()">Remove</button>
            </div>
        `).join('');
    }
    renderEditList();

    // Show modal
    const modal = document.getElementById('editWorkoutModal');
    modal.style.display = 'block';

    // Wire Save Changes button
    document.getElementById('saveEditWorkout').onclick = async function () {
        const name = document.getElementById('editWorkoutName').value.trim();
        const description = document.getElementById('editWorkoutDesc').value.trim();
        const difficulty = document.getElementById('editDifficulty').value;

        if (!name) { showError('Workout name is required'); return; }
        if (editExercises.length === 0) { showError('Add at least one exercise'); return; }

        const success = await updateWorkout(workoutId, name, description, difficulty, editExercises);
        if (success) {
            modal.style.display = 'none';
            await loadWorkouts();
            renderSavedWorkouts(allWorkouts);
        }
    };

    // Wire Cancel button
    document.getElementById('cancelEditWorkout').onclick = function () {
        modal.style.display = 'none';
    };
}

/**
 * Edit a pre-made workout (admin panel)
 */
function editAdminWorkout(workoutId) {
    const workout = allWorkouts.find(w => w.id === workoutId);
    if (!workout) { showError('Workout not found'); return; }

    // Populate modal fields
    document.getElementById('editWorkoutName').value = workout.name;
    document.getElementById('editWorkoutDesc').value = workout.description || '';
    document.getElementById('editDifficulty').value = workout.difficulty || 'beginner';

    // Build editable exercises list inside the modal
    let editExercises = workout.exercises.map(ex => ({
        name: ex.name,
        sets: ex.sets,
        reps: ex.reps,
        weight: ex.weight,
        rest: ex.rest_time,
        notes: ex.notes || ''
    }));

    function renderEditList() {
        const container = document.getElementById('editExercisesList');
        if (editExercises.length === 0) {
            container.innerHTML = '<p class="empty-state">No exercises. Add some!</p>';
            return;
        }
        container.innerHTML = editExercises.map((ex, i) => `
            <div class="exercise-item">
                <div class="exercise-item-info">
                    <h4>${ex.name}</h4>
                    <p>${ex.sets}x${ex.reps}${ex.weight > 0 ? ` @ ${ex.weight}kg` : ''} - Rest: ${ex.rest}s</p>
                </div>
                <button class="btn-remove" onclick="editExercises.splice(${i},1);renderEditList()">Remove</button>
            </div>
        `).join('');
    }
    renderEditList();

    // Show modal
    const modal = document.getElementById('editWorkoutModal');
    modal.style.display = 'block';

    // Wire Save Changes button
    document.getElementById('saveEditWorkout').onclick = async function () {
        const name = document.getElementById('editWorkoutName').value.trim();
        const description = document.getElementById('editWorkoutDesc').value.trim();
        const difficulty = document.getElementById('editDifficulty').value;

        if (!name) { showError('Workout name is required'); return; }
        if (editExercises.length === 0) { showError('Add at least one exercise'); return; }

        const success = await updateWorkout(workoutId, name, description, difficulty, editExercises);
        if (success) {
            modal.style.display = 'none';
            await loadWorkouts();
            renderAdminWorkoutsList(allWorkouts);
        }
    };

    // Wire Cancel button
    document.getElementById('cancelEditWorkout').onclick = function () {
        modal.style.display = 'none';
    };
}

/**
 * Load workouts
 */
async function loadWorkouts() {
    await fetchWorkouts();
    renderWorkoutsList(allWorkouts);
    renderSavedWorkouts(allWorkouts);
}

/**
 * Initialize page
 */
async function initPage() {
    // Load all data
    await loadWorkouts();
    await fetchUserStats();
    
    // Set up event listeners
    setupEventListeners();
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Navigation
    document.querySelectorAll('.nav-link:not(.logout-link)').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            showPage(page);
        });
    });
    
    // Quick action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const page = btn.getAttribute('data-page');
            showPage(page);
        });
    });
    
    // Filter buttons - workouts
    document.querySelectorAll('#workouts .filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('#workouts .filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            renderWorkoutsList(allWorkouts, filter);
        });
    });
    
    // Filter buttons - history
    document.querySelectorAll('#history .filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('#history .filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            renderWorkoutHistory(filter);
        });
    });
    
    // Custom workout form
    document.getElementById('addExerciseBtn').addEventListener('click', addExerciseToCustom);
    document.getElementById('saveWorkout').addEventListener('click', saveCustomWorkoutForm);
    document.getElementById('cancelWorkout').addEventListener('click', () => {
        customExercises = [];
        document.getElementById('workoutName').value = '';
        document.getElementById('workoutDescription').value = '';
        renderCustomExercises();
        showPage('dashboard');
    });
    
    // Admin form (only exists for the fixed admin account)
    const adminAddExerciseBtn = document.getElementById('adminAddExerciseBtn');
    if (adminAddExerciseBtn) {
        adminAddExerciseBtn.addEventListener('click', addExerciseToAdmin);
        document.getElementById('adminSaveWorkout').addEventListener('click', saveAdminWorkoutForm);
        document.getElementById('adminCancelWorkout').addEventListener('click', () => {
            adminExercises = [];
            document.getElementById('adminWorkoutName').value = '';
            document.getElementById('adminWorkoutDesc').value = '';
            renderAdminExercises();
        });

        // Admin tabs
        document.querySelectorAll('.admin-tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const tab = btn.getAttribute('data-tab');
                document.querySelectorAll('.admin-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(tab).classList.add('active');
            });
        });
    }
    
    // Workout execution
    document.getElementById('nextExercise').addEventListener('click', nextExercise);
    document.getElementById('prevExercise').addEventListener('click', previousExercise);
    document.getElementById('completeWorkout').addEventListener('click', completeWorkout);
    document.getElementById('quitWorkout').addEventListener('click', quitWorkout);
    
    document.getElementById('backToWorkouts').addEventListener('click', () => {
        quitWorkout();
    });
    
    // Modal close
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            e.target.closest('.modal').style.display = 'none';
        });
    });
    
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', initPage);
