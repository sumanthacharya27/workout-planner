// GymPlanner Pro - Main Application
class App {
    constructor() {
        this.user = null;
        this.exercises = [];
        this.workouts = [];
        this.currentWorkout = [];
        this.allExercises = [];
        
        this.init();
    }
    
    async init() {
        this.setupEventListeners();
        await this.checkAuthStatus();
    }
    
    setupEventListeners() {
        // Auth modal form toggle
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
                
                e.target.classList.add('active');
                const form = e.target.dataset.form;
                document.getElementById(`${form}Form`).classList.add('active');
            });
        });
        
        // Login form
        document.getElementById('loginForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const email = e.target.querySelector('input[type="email"]').value;
            const password = e.target.querySelector('input[type="password"]').value;
            this.login(email, password, e.target);
        });
        
        // Register form
        document.getElementById('registerForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = e.target.querySelectorAll('input')[0].value;
            const email = e.target.querySelectorAll('input')[1].value;
            const password = e.target.querySelectorAll('input')[2].value;
            this.register(name, email, password, e.target);
        });
        
        // Logout button
        document.getElementById('logoutBtn').addEventListener('click', () => this.logout());
        
        // Navigation
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchPage(e.target.dataset.page);
            });
        });
        
        // Exercise filters
        document.getElementById('muscleFilter').addEventListener('change', () => this.filterExercises());
        document.getElementById('difficultyFilter').addEventListener('change', () => this.filterExercises());
        
        // Workout builder
        document.getElementById('addExerciseBtn').addEventListener('click', () => this.addExerciseToBuilder());
        document.getElementById('saveWorkoutBtn').addEventListener('click', () => this.saveWorkout());
    }
    
    // ===== AUTH =====
    async checkAuthStatus() {
        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'status' })
            });
            
            const data = await response.json();
            if (data.success) {
                this.user = data.data;
                this.showApp();
                await this.loadExercises();
                await this.loadUserWorkouts();
            } else {
                this.showAuthModal();
            }
        } catch (err) {
            console.error('Auth check failed:', err);
            this.showAuthModal();
        }
    }
    
    async login(email, password, form) {
        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'login', email, password })
            });
            
            const data = await response.json();
            const message = form.querySelector('.form-message');
            
            if (data.success) {
                this.user = data.data;
                message.classList.remove('error');
                message.classList.add('success');
                message.textContent = 'Login successful!';
                
                setTimeout(() => {
                    this.showApp();
                    this.loadExercises();
                    this.loadUserWorkouts();
                }, 1000);
            } else {
                message.classList.remove('success');
                message.classList.add('error');
                message.textContent = data.message;
            }
        } catch (err) {
            console.error('Login failed:', err);
        }
    }
    
    async register(name, email, password, form) {
        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'register', name, email, password })
            });
            
            const data = await response.json();
            const message = form.querySelector('.form-message');
            
            if (data.success) {
                message.classList.remove('error');
                message.classList.add('success');
                message.textContent = 'Registration successful! Logging in...';
                
                setTimeout(() => {
                    this.login(email, password, form);
                }, 1500);
            } else {
                message.classList.remove('success');
                message.classList.add('error');
                message.textContent = data.message;
            }
        } catch (err) {
            console.error('Registration failed:', err);
        }
    }
    
    async logout() {
        try {
            await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            });
            
            this.user = null;
            this.showAuthModal();
        } catch (err) {
            console.error('Logout failed:', err);
        }
    }
    
    showAuthModal() {
        document.getElementById('authModal').classList.remove('hidden');
        document.getElementById('mainApp').classList.add('hidden');
    }
    
    showApp() {
        document.getElementById('authModal').classList.add('hidden');
        document.getElementById('mainApp').classList.remove('hidden');
        document.getElementById('userName').textContent = this.user.user_name;
    }
    
    // ===== EXERCISES =====
    async loadExercises() {
        try {
            const response = await fetch('api/exercises.php');
            const data = await response.json();
            
            if (data.success) {
                this.allExercises = data.data.exercises;
                this.populateMuscleFilters();
                this.populateExerciseSelect();
                this.displayExercises(this.allExercises);
            }
        } catch (err) {
            console.error('Failed to load exercises:', err);
        }
    }
    
    populateMuscleFilters() {
        const muscles = [...new Set(this.allExercises.map(e => e.muscle_group))];
        const select = document.getElementById('muscleFilter');
        
        muscles.forEach(muscle => {
            const option = document.createElement('option');
            option.value = muscle;
            option.textContent = muscle;
            select.appendChild(option);
        });
    }
    
    populateExerciseSelect() {
        const select = document.getElementById('exerciseSelect');
        select.innerHTML = '<option value="">Select an exercise...</option>';
        
        this.allExercises.forEach(exercise => {
            const option = document.createElement('option');
            option.value = exercise.id;
            option.textContent = `${exercise.name} (${exercise.muscle_group})`;
            select.appendChild(option);
        });
    }
    
    filterExercises() {
        const muscle = document.getElementById('muscleFilter').value;
        const difficulty = document.getElementById('difficultyFilter').value;
        
        let filtered = this.allExercises;
        
        if (muscle) {
            filtered = filtered.filter(e => e.muscle_group === muscle);
        }
        
        if (difficulty) {
            filtered = filtered.filter(e => e.difficulty === difficulty);
        }
        
        this.displayExercises(filtered);
    }
    
    displayExercises(exercises) {
        const list = document.getElementById('exerciseList');
        list.innerHTML = '';
        
        exercises.forEach(exercise => {
            const item = document.createElement('div');
            item.className = 'exercise-item';
            
            const difficultyClass = `difficulty-${exercise.difficulty.toLowerCase()}`;
            
            item.innerHTML = `
                <div class="exercise-header">
                    <div class="exercise-name">${exercise.name}</div>
                    <span class="exercise-difficulty ${difficultyClass}">${exercise.difficulty}</span>
                </div>
                <div class="exercise-muscle">${exercise.muscle_group}</div>
                <div class="exercise-description">${exercise.description || 'No description'}</div>
            `;
            
            list.appendChild(item);
        });
    }
    
    // ===== WORKOUT BUILDER =====
    addExerciseToBuilder() {
        const selectEl = document.getElementById('exerciseSelect');
        const exerciseId = selectEl.value;
        const sets = parseInt(document.getElementById('setsInput').value) || 3;
        const reps = parseInt(document.getElementById('repsInput').value) || 10;
        const rest = parseInt(document.getElementById('restInput').value) || 60;
        
        if (!exerciseId) {
            alert('Please select an exercise');
            return;
        }
        
        const exercise = this.allExercises.find(e => e.id == exerciseId);
        
        this.currentWorkout.push({
            exercise_id: exerciseId,
            name: exercise.name,
            sets,
            reps,
            rest_seconds: rest
        });
        
        this.updateWorkoutPreview();
        
        // Reset inputs
        selectEl.value = '';
        document.getElementById('setsInput').value = '3';
        document.getElementById('repsInput').value = '10';
        document.getElementById('restInput').value = '60';
    }
    
    removeExerciseFromBuilder(index) {
        this.currentWorkout.splice(index, 1);
        this.updateWorkoutPreview();
    }
    
    updateWorkoutPreview() {
        const preview = document.getElementById('workoutPreview');
        preview.innerHTML = '';
        
        if (this.currentWorkout.length === 0) {
            preview.innerHTML = '<p style="color: var(--text-light); text-align: center; padding: 40px 0;">No exercises added yet</p>';
            return;
        }
        
        this.currentWorkout.forEach((ex, index) => {
            const item = document.createElement('div');
            item.className = 'preview-item';
            item.innerHTML = `
                <div>
                    <div class="preview-item-name">${ex.name}</div>
                    <div class="preview-item-config">${ex.sets}×${ex.reps} • ${ex.rest_seconds}s rest</div>
                </div>
                <button class="preview-item-remove" data-index="${index}">×</button>
            `;
            
            item.querySelector('.preview-item-remove').addEventListener('click', () => {
                this.removeExerciseFromBuilder(index);
            });
            
            preview.appendChild(item);
        });
    }
    
    async saveWorkout() {
        const name = document.getElementById('workoutName').value;
        const description = document.getElementById('workoutDesc').value;
        
        if (!name.trim()) {
            alert('Please enter a workout name');
            return;
        }
        
        if (this.currentWorkout.length === 0) {
            alert('Please add at least one exercise');
            return;
        }
        
        try {
            const response = await fetch('api/workouts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'create',
                    name,
                    description,
                    exercises: this.currentWorkout
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Workout saved successfully!');
                
                // Reset form
                document.getElementById('workoutName').value = '';
                document.getElementById('workoutDesc').value = '';
                this.currentWorkout = [];
                this.updateWorkoutPreview();
                
                // Reload workouts
                this.loadUserWorkouts();
            } else {
                alert('Failed to save workout: ' + data.message);
            }
        } catch (err) {
            console.error('Failed to save workout:', err);
        }
    }
    
    // ===== WORKOUTS =====
    async loadUserWorkouts() {
        try {
            const response = await fetch('api/workouts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.workouts = data.data.workouts;
                this.displayUserWorkouts();
                this.updateDashboard();
            }
        } catch (err) {
            console.error('Failed to load workouts:', err);
        }
    }
    
    displayUserWorkouts() {
        const list = document.getElementById('workoutsList');
        list.innerHTML = '';
        
        if (this.workouts.length === 0) {
            list.innerHTML = '<p style="color: var(--text-light); text-align: center; grid-column: 1/-1; padding: 40px 0;">No workouts yet. Create one in the Builder!</p>';
            return;
        }
        
        this.workouts.forEach(workout => {
            const card = document.createElement('div');
            card.className = 'workout-card';
            
            const created = new Date(workout.created_at).toLocaleDateString();
            
            card.innerHTML = `
                <div class="workout-name">${workout.name}</div>
                <div class="workout-meta">Created: ${created}</div>
                <div class="workout-exercises">${workout.description || 'No description'}</div>
                <div class="workout-actions">
                    <button class="btn btn-secondary" onclick="app.startWorkout(${workout.id})">Start</button>
                    <button class="btn btn-outline" onclick="app.editWorkout(${workout.id})">Edit</button>
                </div>
            `;
            
            list.appendChild(card);
        });
    }
    
    startWorkout(workoutId) {
        alert('Workout execution coming in next phase!');
    }
    
    editWorkout(workoutId) {
        alert('Edit functionality coming in next phase!');
    }
    
    updateDashboard() {
        const stats = document.querySelectorAll('.stat-value');
        stats[0].textContent = this.workouts.length;
        stats[1].textContent = '0'; // This week
        stats[2].textContent = this.currentWorkout.length;
    }
    
    // ===== NAVIGATION =====
    switchPage(pageName) {
        // Update nav buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.page === pageName);
        });
        
        // Update pages
        document.querySelectorAll('.page').forEach(page => {
            page.classList.add('hidden');
        });
        
        document.getElementById(`${pageName}Page`).classList.remove('hidden');
    }
}

// Initialize app when DOM is ready
let app;
document.addEventListener('DOMContentLoaded', () => {
    app = new App();
});
