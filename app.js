// ========================================
// DATA STRUCTURES & STORAGE
// ========================================

// Pre-made workouts data
const preMadeWorkouts = [
    {
        id: 'beginner-full-body',
        name: 'Beginner Full Body',
        difficulty: 'beginner',
        description: 'A complete full-body workout perfect for beginners',
        exercises: [
            { name: 'Squats', sets: 3, reps: 12, weight: 0, rest: 60 },
            { name: 'Push-ups', sets: 3, reps: 10, weight: 0, rest: 60 },
            { name: 'Dumbbell Rows', sets: 3, reps: 12, weight: 10, rest: 60 },
            { name: 'Plank', sets: 3, reps: 30, weight: 0, rest: 45 },
            { name: 'Lunges', sets: 3, reps: 10, weight: 0, rest: 60 }
        ]
    },
    {
        id: 'upper-body-strength',
        name: 'Upper Body Strength',
        difficulty: 'intermediate',
        description: 'Build strength in your chest, back, and arms',
        exercises: [
            { name: 'Bench Press', sets: 4, reps: 8, weight: 60, rest: 90 },
            { name: 'Pull-ups', sets: 4, reps: 8, weight: 0, rest: 90 },
            { name: 'Overhead Press', sets: 3, reps: 10, weight: 40, rest: 75 },
            { name: 'Barbell Rows', sets: 4, reps: 10, weight: 50, rest: 75 },
            { name: 'Bicep Curls', sets: 3, reps: 12, weight: 15, rest: 60 },
            { name: 'Tricep Dips', sets: 3, reps: 12, weight: 0, rest: 60 }
        ]
    },
    {
        id: 'leg-day-power',
        name: 'Leg Day Power',
        difficulty: 'intermediate',
        description: 'Intense leg workout for building lower body strength',
        exercises: [
            { name: 'Barbell Squats', sets: 5, reps: 5, weight: 80, rest: 120 },
            { name: 'Romanian Deadlifts', sets: 4, reps: 8, weight: 70, rest: 90 },
            { name: 'Leg Press', sets: 4, reps: 12, weight: 100, rest: 90 },
            { name: 'Walking Lunges', sets: 3, reps: 12, weight: 20, rest: 75 },
            { name: 'Leg Curls', sets: 3, reps: 15, weight: 30, rest: 60 },
            { name: 'Calf Raises', sets: 4, reps: 20, weight: 40, rest: 60 }
        ]
    },
    {
        id: 'cardio-hiit',
        name: 'HIIT Cardio Blast',
        difficulty: 'advanced',
        description: 'High-intensity interval training for maximum calorie burn',
        exercises: [
            { name: 'Burpees', sets: 4, reps: 15, weight: 0, rest: 30 },
            { name: 'Mountain Climbers', sets: 4, reps: 30, weight: 0, rest: 30 },
            { name: 'Jump Squats', sets: 4, reps: 20, weight: 0, rest: 30 },
            { name: 'High Knees', sets: 4, reps: 40, weight: 0, rest: 30 },
            { name: 'Box Jumps', sets: 3, reps: 15, weight: 0, rest: 45 },
            { name: 'Sprint Intervals', sets: 6, reps: 30, weight: 0, rest: 60 }
        ]
    },
    {
        id: 'core-strength',
        name: 'Core Strength',
        difficulty: 'beginner',
        description: 'Strengthen your core with these targeted exercises',
        exercises: [
            { name: 'Plank', sets: 3, reps: 60, weight: 0, rest: 45 },
            { name: 'Russian Twists', sets: 3, reps: 20, weight: 10, rest: 45 },
            { name: 'Bicycle Crunches', sets: 3, reps: 20, weight: 0, rest: 45 },
            { name: 'Dead Bug', sets: 3, reps: 15, weight: 0, rest: 45 },
            { name: 'Leg Raises', sets: 3, reps: 12, weight: 0, rest: 45 }
        ]
    },
    {
        id: 'powerlifting-basics',
        name: 'Powerlifting Basics',
        difficulty: 'advanced',
        description: 'Master the big three: squat, bench, deadlift',
        exercises: [
            { name: 'Barbell Squat', sets: 5, reps: 3, weight: 100, rest: 180 },
            { name: 'Bench Press', sets: 5, reps: 3, weight: 80, rest: 180 },
            { name: 'Deadlift', sets: 5, reps: 3, weight: 120, rest: 180 },
            { name: 'Overhead Press', sets: 3, reps: 5, weight: 50, rest: 120 }
        ]
    }
];

// Local Storage keys
const STORAGE_KEYS = {
    CUSTOM_WORKOUTS: 'gymplanner_custom_workouts',
    WORKOUT_HISTORY: 'gymplanner_workout_history',
    USER_STATS: 'gymplanner_user_stats'
};

// Current workout session state
let currentWorkout = null;
let currentExerciseIndex = 0;
let restTimerInterval = null;
let workoutStartTime = null;

// ========================================
// UTILITY FUNCTIONS
// ========================================

function getFromStorage(key) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
}

function saveToStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function generateId() {
    return 'workout_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

// ========================================
// NAVIGATION
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize app
    initializeApp();
    
    // Navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            navigateToPage(page);
        });
    });
    
    // Quick action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const page = this.getAttribute('data-page');
            navigateToPage(page);
        });
    });
});

function navigateToPage(page) {
    // Hide all sections
    const sections = document.querySelectorAll('.page-section');
    sections.forEach(section => section.classList.remove('active'));
    
    // Remove active from all nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    
    // Show selected section
    const targetSection = document.getElementById(page);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Set active nav link
    const activeLink = document.querySelector(`.nav-link[data-page="${page}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
    
    // Load page-specific content
    if (page === 'dashboard') {
        loadDashboard();
    } else if (page === 'workouts') {
        loadPreMadeWorkouts();
    } else if (page === 'custom') {
        loadCustomWorkouts();
    } else if (page === 'history') {
        loadHistory();
    } else if (page === 'progress') {
        loadProgress();
    }
}

// ========================================
// INITIALIZATION
// ========================================

function initializeApp() {
    // Initialize storage if needed
    if (!getFromStorage(STORAGE_KEYS.CUSTOM_WORKOUTS)) {
        saveToStorage(STORAGE_KEYS.CUSTOM_WORKOUTS, []);
    }
    if (!getFromStorage(STORAGE_KEYS.WORKOUT_HISTORY)) {
        saveToStorage(STORAGE_KEYS.WORKOUT_HISTORY, []);
    }
    if (!getFromStorage(STORAGE_KEYS.USER_STATS)) {
        saveToStorage(STORAGE_KEYS.USER_STATS, {
            totalWorkouts: 0,
            totalExercises: 0,
            totalTime: 0,
            currentStreak: 0
        });
    }
    
    // Load dashboard by default
    loadDashboard();
    loadPreMadeWorkouts();
    
    // Setup custom workout form
    setupCustomWorkoutForm();
}

// ========================================
// DASHBOARD
// ========================================

function loadDashboard() {
    const stats = getFromStorage(STORAGE_KEYS.USER_STATS);
    const history = getFromStorage(STORAGE_KEYS.WORKOUT_HISTORY) || [];
    
    // Update stat cards
    document.getElementById('totalWorkouts').textContent = stats.totalWorkouts;
    document.getElementById('currentStreak').textContent = stats.currentStreak;
    document.getElementById('totalTime').textContent = Math.floor(stats.totalTime / 60);
    document.getElementById('totalExercises').textContent = stats.totalExercises;
    
    // Load recent workouts
    const recentList = document.getElementById('recentWorkoutsList');
    
    if (history.length === 0) {
        recentList.innerHTML = '<p class="empty-state">No workouts yet. Start your first workout!</p>';
    } else {
        const recentWorkouts = history.slice(-5).reverse();
        recentList.innerHTML = recentWorkouts.map(workout => `
            <div class="recent-item">
                <div class="recent-item-header">
                    <div class="recent-item-name">${workout.name}</div>
                    <div class="recent-item-date">${formatDate(workout.completedAt)}</div>
                </div>
                <div class="recent-item-stats">
                    ${workout.exercises.length} exercises • ${workout.duration} min
                </div>
            </div>
        `).join('');
    }
}

// ========================================
// PRE-MADE WORKOUTS
// ========================================

function loadPreMadeWorkouts(filter = 'all') {
    const workoutsList = document.getElementById('workoutsList');
    
    let filteredWorkouts = preMadeWorkouts;
    if (filter !== 'all') {
        filteredWorkouts = preMadeWorkouts.filter(w => w.difficulty === filter);
    }
    
    workoutsList.innerHTML = filteredWorkouts.map(workout => `
        <div class="workout-card" onclick="startPreMadeWorkout('${workout.id}')">
            <div class="workout-card-header">
                <div class="workout-card-title">${workout.name}</div>
                <span class="workout-badge ${workout.difficulty}">${workout.difficulty}</span>
            </div>
            <div class="workout-card-description">${workout.description}</div>
            <div class="workout-card-info">
                <span>📝 ${workout.exercises.length} exercises</span>
                <span>⏱️ ~${estimateWorkoutDuration(workout.exercises)} min</span>
            </div>
        </div>
    `).join('');
    
    // Setup filter buttons
    const filterButtons = document.querySelectorAll('#workouts .filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            loadPreMadeWorkouts(filter);
        });
    });
}

function estimateWorkoutDuration(exercises) {
    let totalSeconds = 0;
    exercises.forEach(ex => {
        // Assume 3 seconds per rep, plus rest time
        totalSeconds += (ex.sets * ex.reps * 3) + (ex.sets * ex.rest);
    });
    return Math.round(totalSeconds / 60);
}

function startPreMadeWorkout(workoutId) {
    const workout = preMadeWorkouts.find(w => w.id === workoutId);
    if (workout) {
        startWorkout(workout);
    }
}

// ========================================
// CUSTOM WORKOUT BUILDER
// ========================================

let currentExercises = [];

function setupCustomWorkoutForm() {
    const addExerciseBtn = document.getElementById('addExerciseBtn');
    const saveWorkoutBtn = document.getElementById('saveWorkout');
    const cancelBtn = document.getElementById('cancelWorkout');
    const modal = document.getElementById('exerciseModal');
    const closeModal = document.querySelector('.close');
    const saveExerciseBtn = document.getElementById('saveExercise');
    
    addExerciseBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });
    
    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        clearExerciseForm();
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            clearExerciseForm();
        }
    });
    
    saveExerciseBtn.addEventListener('click', addExerciseToList);
    
    saveWorkoutBtn.addEventListener('click', saveCustomWorkout);
    
    cancelBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
            clearWorkoutForm();
        }
    });
}

function addExerciseToList() {
    const name = document.getElementById('exerciseName').value.trim();
    const sets = parseInt(document.getElementById('exerciseSets').value);
    const reps = parseInt(document.getElementById('exerciseReps').value);
    const weight = parseFloat(document.getElementById('exerciseWeight').value) || 0;
    const rest = parseInt(document.getElementById('exerciseRest').value);
    const notes = document.getElementById('exerciseNotes').value.trim();
    
    if (!name) {
        alert('Please enter an exercise name');
        return;
    }
    
    const exercise = { name, sets, reps, weight, rest, notes };
    currentExercises.push(exercise);
    
    renderExercisesList();
    
    // Close modal and clear form
    document.getElementById('exerciseModal').style.display = 'none';
    clearExerciseForm();
}

function renderExercisesList() {
    const exercisesList = document.getElementById('exercisesList');
    
    if (currentExercises.length === 0) {
        exercisesList.innerHTML = '<p class="empty-state">No exercises added yet. Click "Add Exercise" to start.</p>';
        return;
    }
    
    exercisesList.innerHTML = currentExercises.map((ex, index) => `
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
            <div class="exercise-actions">
                <button class="btn-icon-small" onclick="removeExercise(${index})">🗑️</button>
            </div>
        </div>
    `).join('');
}

function removeExercise(index) {
    currentExercises.splice(index, 1);
    renderExercisesList();
}

function clearExerciseForm() {
    document.getElementById('exerciseName').value = '';
    document.getElementById('exerciseSets').value = 3;
    document.getElementById('exerciseReps').value = 10;
    document.getElementById('exerciseWeight').value = '';
    document.getElementById('exerciseRest').value = 60;
    document.getElementById('exerciseNotes').value = '';
}

function clearWorkoutForm() {
    document.getElementById('workoutName').value = '';
    document.getElementById('workoutDescription').value = '';
    currentExercises = [];
    renderExercisesList();
}

function saveCustomWorkout() {
    const name = document.getElementById('workoutName').value.trim();
    const description = document.getElementById('workoutDescription').value.trim();
    
    if (!name) {
        alert('Please enter a workout name');
        return;
    }
    
    if (currentExercises.length === 0) {
        alert('Please add at least one exercise');
        return;
    }
    
    const workout = {
        id: generateId(),
        name,
        description,
        exercises: [...currentExercises],
        createdAt: Date.now()
    };
    
    const customWorkouts = getFromStorage(STORAGE_KEYS.CUSTOM_WORKOUTS) || [];
    customWorkouts.push(workout);
    saveToStorage(STORAGE_KEYS.CUSTOM_WORKOUTS, customWorkouts);
    
    alert('Workout saved successfully!');
    clearWorkoutForm();
    loadCustomWorkouts();
}

function loadCustomWorkouts() {
    const customWorkouts = getFromStorage(STORAGE_KEYS.CUSTOM_WORKOUTS) || [];
    const savedList = document.getElementById('savedWorkoutsList');
    
    if (customWorkouts.length === 0) {
        savedList.innerHTML = '<p class="empty-state">No custom workouts yet. Create your first one!</p>';
        return;
    }
    
    savedList.innerHTML = customWorkouts.map(workout => `
        <div class="saved-item">
            <div class="saved-item-header">
                <div class="saved-item-name">${workout.name}</div>
                <div class="saved-item-actions">
                    <button class="btn-icon-small" onclick="deleteCustomWorkout('${workout.id}')">🗑️</button>
                </div>
            </div>
            <div class="saved-item-info">
                ${workout.description || 'No description'}
                <br>
                ${workout.exercises.length} exercises
            </div>
            <div class="saved-item-buttons">
                <button class="btn-small primary" onclick="startCustomWorkout('${workout.id}')">Start Workout</button>
                <button class="btn-small secondary" onclick="viewWorkoutDetails('${workout.id}')">View Details</button>
            </div>
        </div>
    `).join('');
}

function deleteCustomWorkout(workoutId) {
    if (!confirm('Are you sure you want to delete this workout?')) {
        return;
    }
    
    let customWorkouts = getFromStorage(STORAGE_KEYS.CUSTOM_WORKOUTS) || [];
    customWorkouts = customWorkouts.filter(w => w.id !== workoutId);
    saveToStorage(STORAGE_KEYS.CUSTOM_WORKOUTS, customWorkouts);
    loadCustomWorkouts();
}

function startCustomWorkout(workoutId) {
    const customWorkouts = getFromStorage(STORAGE_KEYS.CUSTOM_WORKOUTS) || [];
    const workout = customWorkouts.find(w => w.id === workoutId);
    if (workout) {
        startWorkout(workout);
    }
}

function viewWorkoutDetails(workoutId) {
    const customWorkouts = getFromStorage(STORAGE_KEYS.CUSTOM_WORKOUTS) || [];
    const workout = customWorkouts.find(w => w.id === workoutId);
    if (workout) {
        alert(`${workout.name}\n\nExercises:\n${workout.exercises.map(ex => 
            `• ${ex.name}: ${ex.sets}×${ex.reps}${ex.weight > 0 ? ` @ ${ex.weight}kg` : ''}`
        ).join('\n')}`);
    }
}

// ========================================
// WORKOUT EXECUTION
// ========================================

function startWorkout(workout) {
    currentWorkout = workout;
    currentExerciseIndex = 0;
    workoutStartTime = Date.now();
    
    // Initialize completed sets tracking
    currentWorkout.exercises.forEach(ex => {
        ex.completedSets = 0;
        ex.setCheckboxes = new Array(ex.sets).fill(false);
    });
    
    navigateToPage('execute');
    displayExercise();
    
    // Setup back button
    document.getElementById('backToWorkouts').addEventListener('click', () => {
        if (confirm('Are you sure you want to quit this workout? Your progress will not be saved.')) {
            navigateToPage('workouts');
        }
    });
    
    // Setup navigation buttons
    document.getElementById('nextExercise').addEventListener('click', nextExercise);
    document.getElementById('prevExercise').addEventListener('click', prevExercise);
    document.getElementById('completeWorkout').addEventListener('click', completeWorkout);
    document.getElementById('quitWorkout').addEventListener('click', () => {
        if (confirm('Are you sure you want to quit? Progress will not be saved.')) {
            navigateToPage('workouts');
        }
    });
}

function displayExercise() {
    const exercise = currentWorkout.exercises[currentExerciseIndex];
    const exerciseDisplay = document.getElementById('exerciseDisplay');
    const progressBar = document.getElementById('workoutProgressBar');
    const currentNum = document.getElementById('currentExerciseNum');
    const totalNum = document.getElementById('totalExercisesNum');
    
    // Update workout name
    document.getElementById('executeWorkoutName').textContent = currentWorkout.name;
    
    // Update progress
    const progress = ((currentExerciseIndex + 1) / currentWorkout.exercises.length) * 100;
    progressBar.style.width = progress + '%';
    currentNum.textContent = currentExerciseIndex + 1;
    totalNum.textContent = currentWorkout.exercises.length;
    
    // Display exercise
    exerciseDisplay.innerHTML = `
        <div class="exercise-title">${exercise.name}</div>
        <div class="exercise-stats">
            <div class="stat-box">
                <div class="stat-value">${exercise.sets}</div>
                <div class="stat-name">Sets</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">${exercise.reps}</div>
                <div class="stat-name">Reps</div>
            </div>
            ${exercise.weight > 0 ? `
                <div class="stat-box">
                    <div class="stat-value">${exercise.weight}</div>
                    <div class="stat-name">kg</div>
                </div>
            ` : ''}
            <div class="stat-box">
                <div class="stat-value">${exercise.rest}</div>
                <div class="stat-name">Rest (sec)</div>
            </div>
        </div>
        
        <div class="sets-tracker">
            <h3>Complete Sets</h3>
            <div class="sets-grid">
                ${Array.from({length: exercise.sets}, (_, i) => `
                    <label class="set-checkbox ${exercise.setCheckboxes[i] ? 'completed' : ''}" data-set="${i}">
                        <input type="checkbox" ${exercise.setCheckboxes[i] ? 'checked' : ''} onchange="toggleSet(${i})">
                        Set ${i + 1}
                    </label>
                `).join('')}
            </div>
            <div class="rest-timer" id="restTimer" style="display: none;">
                <p>Rest Timer</p>
                <div class="timer-display" id="timerDisplay">00:00</div>
            </div>
        </div>
    `;
    
    // Show/hide navigation buttons
    document.getElementById('prevExercise').style.display = currentExerciseIndex > 0 ? 'block' : 'none';
    
    const isLastExercise = currentExerciseIndex === currentWorkout.exercises.length - 1;
    document.getElementById('nextExercise').style.display = isLastExercise ? 'none' : 'block';
    document.getElementById('completeWorkout').style.display = isLastExercise ? 'block' : 'none';
}

function toggleSet(setIndex) {
    const exercise = currentWorkout.exercises[currentExerciseIndex];
    exercise.setCheckboxes[setIndex] = !exercise.setCheckboxes[setIndex];
    
    if (exercise.setCheckboxes[setIndex]) {
        exercise.completedSets++;
        // Start rest timer
        startRestTimer(exercise.rest);
    } else {
        exercise.completedSets--;
        stopRestTimer();
    }
    
    displayExercise();
}

function startRestTimer(seconds) {
    stopRestTimer();
    
    const restTimer = document.getElementById('restTimer');
    const timerDisplay = document.getElementById('timerDisplay');
    
    restTimer.style.display = 'block';
    restTimer.classList.add('active');
    
    let remaining = seconds;
    
    const updateTimer = () => {
        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        timerDisplay.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        
        if (remaining <= 0) {
            stopRestTimer();
            // Optional: Play sound or vibrate
        } else {
            remaining--;
        }
    };
    
    updateTimer();
    restTimerInterval = setInterval(updateTimer, 1000);
}

function stopRestTimer() {
    if (restTimerInterval) {
        clearInterval(restTimerInterval);
        restTimerInterval = null;
    }
    
    const restTimer = document.getElementById('restTimer');
    if (restTimer) {
        restTimer.style.display = 'none';
        restTimer.classList.remove('active');
    }
}

function nextExercise() {
    if (currentExerciseIndex < currentWorkout.exercises.length - 1) {
        currentExerciseIndex++;
        stopRestTimer();
        displayExercise();
    }
}

function prevExercise() {
    if (currentExerciseIndex > 0) {
        currentExerciseIndex--;
        stopRestTimer();
        displayExercise();
    }
}

function completeWorkout() {
    const duration = Math.floor((Date.now() - workoutStartTime) / 1000 / 60); // minutes
    
    const workoutSession = {
        id: generateId(),
        name: currentWorkout.name,
        exercises: currentWorkout.exercises.map(ex => ({
            name: ex.name,
            sets: ex.sets,
            reps: ex.reps,
            weight: ex.weight,
            completedSets: ex.completedSets
        })),
        duration: duration,
        completedAt: Date.now()
    };
    
    // Save to history
    const history = getFromStorage(STORAGE_KEYS.WORKOUT_HISTORY) || [];
    history.push(workoutSession);
    saveToStorage(STORAGE_KEYS.WORKOUT_HISTORY, history);
    
    // Update stats
    const stats = getFromStorage(STORAGE_KEYS.USER_STATS);
    stats.totalWorkouts++;
    stats.totalExercises += currentWorkout.exercises.length;
    stats.totalTime += duration;
    stats.currentStreak = calculateStreak(history);
    saveToStorage(STORAGE_KEYS.USER_STATS, stats);
    
    alert(`Workout completed! 🎉\n\nDuration: ${duration} minutes\nExercises: ${currentWorkout.exercises.length}`);
    
    navigateToPage('dashboard');
}

function calculateStreak(history) {
    if (history.length === 0) return 0;
    
    const today = new Date().setHours(0, 0, 0, 0);
    const sortedHistory = history.sort((a, b) => b.completedAt - a.completedAt);
    
    let streak = 0;
    let currentDate = today;
    
    for (const workout of sortedHistory) {
        const workoutDate = new Date(workout.completedAt).setHours(0, 0, 0, 0);
        const daysDiff = Math.floor((currentDate - workoutDate) / (1000 * 60 * 60 * 24));
        
        if (daysDiff === 0 || daysDiff === 1) {
            if (daysDiff === 1) streak++;
            currentDate = workoutDate;
        } else {
            break;
        }
    }
    
    return streak;
}

// ========================================
// HISTORY
// ========================================

function loadHistory(filter = 'all') {
    const history = getFromStorage(STORAGE_KEYS.WORKOUT_HISTORY) || [];
    const historyList = document.getElementById('historyList');
    
    if (history.length === 0) {
        historyList.innerHTML = '<p class="empty-state">No workout history yet. Complete your first workout!</p>';
        return;
    }
    
    let filteredHistory = [...history].reverse();
    
    if (filter === 'week') {
        const weekAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
        filteredHistory = filteredHistory.filter(w => w.completedAt >= weekAgo);
    } else if (filter === 'month') {
        const monthAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
        filteredHistory = filteredHistory.filter(w => w.completedAt >= monthAgo);
    }
    
    historyList.innerHTML = filteredHistory.map(workout => `
        <div class="history-item">
            <div class="history-header">
                <div class="history-title">${workout.name}</div>
                <div class="history-date">${formatDate(workout.completedAt)}</div>
            </div>
            <div class="history-exercises">
                ${workout.exercises.map(ex => 
                    `${ex.name}: ${ex.completedSets}/${ex.sets} sets`
                ).join(' • ')}
            </div>
            <div class="history-stats">
                <span>Duration: ${workout.duration} min</span>
                <span>Exercises: ${workout.exercises.length}</span>
            </div>
        </div>
    `).join('');
    
    // Setup filter buttons
    const filterButtons = document.querySelectorAll('#history .filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            loadHistory(filter);
        });
    });
}

// ========================================
// PROGRESS & CHARTS
// ========================================

function loadProgress() {
    const history = getFromStorage(STORAGE_KEYS.WORKOUT_HISTORY) || [];
    
    if (history.length === 0) {
        return;
    }
    
    // Load charts
    loadFrequencyChart(history);
    loadExerciseChart(history);
    loadPersonalRecords(history);
    checkAchievements();
}

function loadFrequencyChart(history) {
    const ctx = document.getElementById('frequencyChart');
    if (!ctx) return;
    
    // Get last 7 days
    const days = [];
    const counts = [];
    
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toLocaleDateString('en-US', { weekday: 'short' });
        days.push(dateStr);
        
        const dayStart = new Date(date).setHours(0, 0, 0, 0);
        const dayEnd = new Date(date).setHours(23, 59, 59, 999);
        
        const count = history.filter(w => 
            w.completedAt >= dayStart && w.completedAt <= dayEnd
        ).length;
        
        counts.push(count);
    }
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                label: 'Workouts',
                data: counts,
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}

function loadExerciseChart(history) {
    const ctx = document.getElementById('exerciseChart');
    if (!ctx) return;
    
    // Count exercise frequency
    const exerciseCounts = {};
    
    history.forEach(workout => {
        workout.exercises.forEach(ex => {
            exerciseCounts[ex.name] = (exerciseCounts[ex.name] || 0) + 1;
        });
    });
    
    // Get top 5 exercises
    const sortedExercises = Object.entries(exerciseCounts)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5);
    
    const labels = sortedExercises.map(e => e[0]);
    const data = sortedExercises.map(e => e[1]);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)',
                    'rgba(72, 187, 120, 0.8)',
                    'rgba(237, 137, 54, 0.8)',
                    'rgba(245, 101, 101, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
}

function loadPersonalRecords(history) {
    const prsList = document.getElementById('prsList');
    
    // Find max weight for each exercise
    const exercisePRs = {};
    
    history.forEach(workout => {
        workout.exercises.forEach(ex => {
            if (ex.weight > 0) {
                if (!exercisePRs[ex.name] || ex.weight > exercisePRs[ex.name]) {
                    exercisePRs[ex.name] = ex.weight;
                }
            }
        });
    });
    
    const prs = Object.entries(exercisePRs);
    
    if (prs.length === 0) {
        prsList.innerHTML = '<p class="empty-state">Complete workouts with weights to track your personal records!</p>';
        return;
    }
    
    prsList.innerHTML = prs.map(([exercise, weight]) => `
        <div class="pr-item">
            <div class="pr-exercise">${exercise}</div>
            <div class="pr-value">${weight} kg</div>
        </div>
    `).join('');
}

function checkAchievements() {
    const stats = getFromStorage(STORAGE_KEYS.USER_STATS);
    const achievements = document.querySelectorAll('.achievement-card');
    
    // First Workout
    if (stats.totalWorkouts >= 1) {
        achievements[0].classList.remove('locked');
        achievements[0].classList.add('unlocked');
    }
    
    // Week Warrior
    if (stats.currentStreak >= 7) {
        achievements[1].classList.remove('locked');
        achievements[1].classList.add('unlocked');
    }
    
    // Century Club
    if (stats.totalWorkouts >= 100) {
        achievements[2].classList.remove('locked');
        achievements[2].classList.add('unlocked');
    }
    
    // Consistency King
    if (stats.currentStreak >= 30) {
        achievements[3].classList.remove('locked');
        achievements[3].classList.add('unlocked');
    }
}
