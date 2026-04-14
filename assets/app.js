// ============================================
// GYM PLANNER - FULL STACK VERSION
// ============================================
// Uses PHP backend and MySQL database

// ============================================
// CONFIGURATION
// ============================================
const API_BASE = '/gym_workout-planner';  // App lives at localhost/gym_workout-planner/

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
let progressCharts = {
    frequency: null,
    workoutDistribution: null,
    duration: null,
    difficulty: null
};
let currentProgressRange = 30;

// ============================================
// API FUNCTIONS
// ============================================

/**
 * Fetch all workouts from backend
 */
async function fetchWorkouts() {
    try {
        const response = await fetch(`${API_BASE}/api/get_workouts.php`, {
            credentials: 'include'
        });
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
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name,
                description,
                exercises,
                csrf_token: window.APP_CONFIG.csrfToken
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
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                workoutId,
                duration,
                notes,
                csrf_token: window.APP_CONFIG.csrfToken
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
async function fetchWorkoutHistory(filter = 'all', limit = null) {
    try {
        let url = `${API_BASE}/api/get_history.php?filter=${filter}`;
        if (limit) url += `&limit=${limit}`;

        const response = await fetch(url, {
            credentials: 'include'
        });
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
        const response = await fetch(`${API_BASE}/api/get_stats.php`, {
            credentials: 'include'
        });
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
 * Fetch detailed progress analytics for charts and milestones
 */
async function fetchProgressData(range = 30) {
    try {
        const response = await fetch(`${API_BASE}/api/get_progress.php?range=${range}`, {
            credentials: 'include'
        });
        const result = await response.json();

        if (result.success) {
            return result;
        }

        showError(result.error || 'Failed to load progress');
        return null;
    } catch (error) {
        console.error('Error fetching progress data:', error);
        showError('Error loading progress analytics');
        return null;
    }
}

/**
 * Admin: Save pre-made workout
 */
async function adminSaveWorkout(name, description, difficulty, exercises) {
    try {
        const response = await fetch(`${API_BASE}/api/admin_save_workout.php`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name,
                description,
                difficulty,
                exercises,
                csrf_token: window.APP_CONFIG.csrfToken
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
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                workoutId: workoutId,
                csrf_token: window.APP_CONFIG.csrfToken
            })
        })

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
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                workoutId,
                name,
                description,
                difficulty,
                exercises,
                csrf_token: window.APP_CONFIG.csrfToken
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
        <div class="workout-card-header">
            <h3 class="workout-card-title">${workout.name}</h3>
            <span class="workout-badge ${workout.difficulty}">${workout.difficulty}</span>
        </div>
        <p class="workout-card-description">${workout.description || ''}</p>
        <div class="workout-card-info">
            <span>
                <span>🏋️</span>
                <span>${workout.exercises.length} exercises</span>
            </span>
        </div>
        <div style="margin-top: 1.5rem;">
            <button class="btn-save btn-primary" onclick="startWorkout(${workout.id})">Start Workout</button>
        </div>
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
async function loadRecentWorkouts() {


    const container = document.getElementById('recentWorkoutsList');

    try {
        const history = await fetchWorkoutHistory('all', 5);

        if (!history || history.length === 0) {
            container.innerHTML = `<p class="empty-state">No workouts yet. Start your first workout!</p>`;
            return;
        }

        const recent = history;

        container.innerHTML = recent.map(item => `
            <div class="recent-item">
                <div class="recent-info">
                    <h3>${item.workout_name}</h3>
                    <p>${new Date(item.completed_at).toLocaleString()}</p>
                </div>
                <div class="recent-meta">
                    ⏱️ ${Math.round(item.duration / 60)} mins<br>
                    💪 ${item.exercise_count} exercises
                </div>
            </div>
        `).join('');

    } catch (err) {
        console.error("Error loading recent workouts:", err);
        container.innerHTML = `<p class="empty-state">Failed to load workouts</p>`;
    }
}

/**
 * Render admin workouts list
 */
function renderAdminWorkoutsList(workouts) {
    const container = document.getElementById('adminWorkoutsList');
    if (!container) return;

    let premade = (workouts || []).filter(w => !w.is_custom);

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

function createOrUpdateChart(chartKey, canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;

    if (progressCharts[chartKey]) {
        progressCharts[chartKey].destroy();
    }

    progressCharts[chartKey] = new Chart(canvas, config);
}

function renderProgressSummary(progressData) {
    const stats = progressData.stats || {};
    const weekSummary = progressData.week_summary || {};
    const dailyData = progressData.daily_data || [];
    const rangeWorkouts = dailyData.reduce((sum, day) => sum + Number(day.workout_count || 0), 0);

    document.getElementById('progressTotalWorkouts').textContent = stats.total_workouts || 0;
    document.getElementById('progressCurrentStreak').textContent = stats.current_streak || 0;
    document.getElementById('progressWeekTime').textContent = `${Math.round((weekSummary.week_time || 0) / 60)}m`;
    document.getElementById('progressRangeWorkouts').textContent = rangeWorkouts;
}

function renderPersonalRecords(records = []) {
    const container = document.getElementById('prsList');

    if (!records.length) {
        container.innerHTML = '<p class="empty-state">Complete workouts with weights to unlock personal records.</p>';
        return;
    }

    container.innerHTML = records.map(record => `
        <div class="pr-item">
            <div class="pr-exercise">${record.exercise_name}</div>
            <div class="pr-value">${Number(record.best_weight).toFixed(1)} kg</div>
            <div class="history-stats">
                <span>${record.sets} × ${record.reps}</span>
                <span>Volume: ${Math.round(record.best_volume || 0)} kg</span>
            </div>
        </div>
    `).join('');
}

function renderAchievements(stats = {}) {
    const totalWorkouts = Number(stats.total_workouts || 0);
    const currentStreak = Number(stats.current_streak || 0);

    const achievements = [
        {
            icon: '🏆',
            name: 'First Workout',
            desc: 'Complete your first workout',
            unlocked: totalWorkouts >= 1
        },
        {
            icon: '🔥',
            name: 'Week Warrior',
            desc: '7 day workout streak',
            unlocked: currentStreak >= 7
        },
        {
            icon: '💪',
            name: 'Century Club',
            desc: 'Complete 100 workouts',
            unlocked: totalWorkouts >= 100
        },
        {
            icon: '⚡',
            name: 'Consistency King',
            desc: '30 day workout streak',
            unlocked: currentStreak >= 30
        }
    ];

    const container = document.getElementById('achievementsList');
    container.innerHTML = achievements.map(item => `
        <div class="achievement-card ${item.unlocked ? 'unlocked' : 'locked'}">
            <div class="achievement-icon">${item.icon}</div>
            <div class="achievement-name">${item.name}</div>
            <div class="achievement-desc">${item.desc}</div>
        </div>
    `).join('');
}

function renderProgressCharts(progressData) {
    const dailyData = progressData.daily_data || [];
    const sessionData = progressData.session_data || [];
    const workoutDist = progressData.workout_dist || [];
    const difficultyDist = progressData.difficulty_dist || [];

    const freqLabels = dailyData.map(d => new Date(d.workout_date).toLocaleDateString());
    const freqValues = dailyData.map(d => Number(d.workout_count || 0));

    createOrUpdateChart('frequency', 'frequencyChart', {
        type: 'line',
        data: {
            labels: freqLabels,
            datasets: [{
                label: 'Workouts',
                data: freqValues,
                borderColor: '#4299e1',
                backgroundColor: 'rgba(66, 153, 225, 0.15)',
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    createOrUpdateChart('workoutDistribution', 'exerciseChart', {
        type: 'pie',
        data: {
            labels: workoutDist.map(w => w.name),
            datasets: [{
                data: workoutDist.map(w => Number(w.count || 0)),
                backgroundColor: ['#4299e1', '#48bb78', '#ed8936', '#9f7aea', '#f56565', '#38b2ac']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    createOrUpdateChart('duration', 'durationChart', {
        type: 'bar',
        data: {
            labels: sessionData.map(item => new Date(item.completed_at).toLocaleDateString()),
            datasets: [{
                label: 'Minutes',
                data: sessionData.map(item => Math.round(Number(item.duration || 0) / 60)),
                backgroundColor: '#48bb78'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Minutes' }
                }
            }
        }
    });

    createOrUpdateChart('difficulty', 'difficultyChart', {
        type: 'doughnut',
        data: {
            labels: difficultyDist.map(d => d.difficulty || 'unknown'),
            datasets: [{
                data: difficultyDist.map(d => Number(d.count || 0)),
                backgroundColor: ['#68d391', '#f6ad55', '#fc8181', '#a0aec0']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

async function loadProgressDashboard(range = currentProgressRange) {
    const progressData = await fetchProgressData(range);
    if (!progressData) return;

    currentProgressRange = range;
    renderProgressSummary(progressData);
    renderProgressCharts(progressData);
    renderPersonalRecords(progressData.personal_records || []);
    renderAchievements(progressData.stats || {});
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
    // Check for admin authentication with case-insensitivity
    const userRole = (window.APP_CONFIG && window.APP_CONFIG.role) ? window.APP_CONFIG.role.toLowerCase().trim() : 'user';

    if (pageId === 'admin' && userRole !== 'admin') {
        showPage('dashboard');
        showError('Access denied: Administrators only.');
        return;
    }

    const targetSection = document.getElementById(pageId);
    if (!targetSection) {
        console.warn(`Page section "${pageId}" not found in DOM.`);
        if (pageId !== 'dashboard') showPage('dashboard');
        return;
    }

    document.querySelectorAll('.page-section').forEach(section => {
        section.classList.remove('active');
    });

    targetSection.classList.add('active');

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
    } else if (pageId === 'progress') {
        loadProgressDashboard(currentProgressRange);
    } else if (pageId === 'admin') {
        renderAdminWorkoutsList(allWorkouts);
    } else if (pageId === 'dashboard') {
        loadRecentWorkouts();
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
    document.getElementById('saveExercise').onclick = function () {
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
    document.getElementById('saveExercise').onclick = function () {
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

// ============================================
// BODY CALCULATOR FUNCTIONS
// ============================================

/**
 * Calculate BMI and update UI
 */
function calculateBMI() {
    const height = parseFloat(document.getElementById('bmiHeight').value);
    const weight = parseFloat(document.getElementById('bmiWeight').value);
    const age = parseInt(document.getElementById('bmiAge').value);

    if (!height || !weight || isNaN(age)) {
        showError('Please fill in all fields correctly');
        return;
    }

    const bmi = weight / ((height / 100) ** 2);
    const bmiValue = bmi.toFixed(1);

    let category = '';
    let advice = '';
    let categoryIcon = '';
    let color = '';
    let markerPercent = 0;

    if (bmi < 18.5) {
        category = 'Underweight';
        advice = 'You are in the underweight range. Focus on a calorie surplus with nutritious whole foods and strength training to build muscle.';
        categoryIcon = '⚠️';
        color = '#1B98E0';
        markerPercent = (bmi / 18.5) * 20; // Scale to 0-25%
    } else if (bmi < 25) {
        category = 'Normal';
        advice = 'Great job! You are in the healthy weight range. Maintain your current lifestyle and stay active to keep your BMI in this zone.';
        categoryIcon = '✅';
        color = '#06D6A0';
        markerPercent = 25 + ((bmi - 18.5) / 6.5) * 25; // Scale to 25-50%
    } else if (bmi < 30) {
        category = 'Overweight';
        advice = 'You are in the overweight range. Small changes in diet and increasing physical activity can help you reach a healthier range.';
        categoryIcon = '⚠️';
        color = '#F7931E';
        markerPercent = 50 + ((bmi - 25) / 5) * 25; // Scale to 50-75%
    } else {
        category = 'Obese';
        advice = 'You are in the obese range. We recommend consulting a healthcare provider or a nutritionist to create a safe weight management plan.';
        categoryIcon = '🚨';
        color = '#EF476F';
        markerPercent = 75 + Math.min(((bmi - 30) / 10) * 25, 25); // Scale to 75-100%
    }

    // Update UI
    document.getElementById('bmiResult').style.display = 'block';
    document.getElementById('bmiValue').textContent = bmiValue;
    document.getElementById('bmiCategory').textContent = category;
    document.getElementById('bmiCategoryIcon').textContent = categoryIcon;
    document.getElementById('bmiAdvice').textContent = advice;
    document.getElementById('bmiAdvice').style.borderLeftColor = color;

    // Position marker
    const marker = document.getElementById('bmiMarker');
    marker.style.left = `${Math.min(Math.max(markerPercent, 5), 95)}%`;

    // Calculate healthy weight range (BMI 18.5 - 24.9)
    const minWeight = (18.5 * ((height / 100) ** 2)).toFixed(1);
    const maxWeight = (24.9 * ((height / 100) ** 2)).toFixed(1);
    document.getElementById('bmiHealthyRange').textContent = `${minWeight} - ${maxWeight}`;

    showSuccess('BMI Calculated!');
}

/**
 * Calculate BMR and TDEE
 */
function calculateBMR() {
    const gender = document.getElementById('bmrGender').value;
    const age = parseInt(document.getElementById('bmrAge').value);
    const height = parseFloat(document.getElementById('bmrHeight').value);
    const weight = parseFloat(document.getElementById('bmrWeight').value);
    const activity = parseFloat(document.getElementById('bmrActivity').value);

    if (!age || !height || !weight) {
        showError('Please fill in all fields correctly');
        return;
    }

    // Mifflin-St Jeor Equation
    let bmr = (10 * weight) + (6.25 * height) - (5 * age);
    if (gender === 'male') {
        bmr += 5;
    } else {
        bmr -= 161;
    }

    const tdee = Math.round(bmr * activity);

    // Update UI
    document.getElementById('bmrResult').style.display = 'block';
    document.getElementById('bmrValue').textContent = Math.round(bmr).toLocaleString();
    document.getElementById('tdeeValue').textContent = tdee.toLocaleString();

    // Render Goals
    const goalsContainer = document.getElementById('bmrGoals');
    const goals = [
        { label: 'Weight Loss (0.5kg/week)', calories: tdee - 500, class: 'beginner' },
        { label: 'Maintenance', calories: tdee, class: 'intermediate' },
        { label: 'Muscle Gain (Surplus)', calories: tdee + 300, class: 'advanced' }
    ];

    goalsContainer.innerHTML = goals.map(goal => `
        <div class="exercise-item" style="border-left: 5px solid var(--${goal.class === 'beginner' ? 'primary' : goal.class === 'intermediate' ? 'success' : 'danger'});">
            <div class="exercise-item-info">
                <h4>${goal.label}</h4>
                <p><strong>${goal.calories.toLocaleString()} kcal</strong> per day</p>
            </div>
        </div>
    `).join('');

    showSuccess('BMR & TDEE Calculated!');
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
    await loadRecentWorkouts();

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

    // Filter buttons - progress range
    document.querySelectorAll('#progress .progress-range-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('#progress .progress-range-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const range = parseInt(btn.getAttribute('data-range'), 10) || 30;
            loadProgressDashboard(range);
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

    // Admin form - wrap all admin listeners in null checks
    const adminAddExerciseBtn = document.getElementById('adminAddExerciseBtn');
    const adminSaveWorkout = document.getElementById('adminSaveWorkout');
    const adminCancelWorkout = document.getElementById('adminCancelWorkout');

    if (adminAddExerciseBtn) adminAddExerciseBtn.addEventListener('click', addExerciseToAdmin);
    if (adminSaveWorkout) adminSaveWorkout.addEventListener('click', saveAdminWorkoutForm);
    if (adminCancelWorkout) adminCancelWorkout.addEventListener('click', () => {
        adminExercises = [];
        document.getElementById('adminWorkoutName').value = '';
        document.getElementById('adminWorkoutDesc').value = '';
        renderAdminExercises();
    });

    // Admin & Calculator tabs
    document.querySelectorAll('.admin-tab-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            // Handle both data-tab (admin) and data-calc-tab (calculator)
            const tab = btn.getAttribute('data-tab') || btn.getAttribute('data-calc-tab');
            if (!tab) return;

            // Get all tab buttons and contents in the same container/context
            const container = btn.parentElement;
            container.querySelectorAll('.admin-tab-btn').forEach(b => b.classList.remove('active'));

            // Get all tab contents related to this set of tabs
            const section = btn.closest('.page-section');
            if (section) {
                section.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
            }

            btn.classList.add('active');
            document.getElementById(tab)?.classList.add('active');
        });
    });


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
