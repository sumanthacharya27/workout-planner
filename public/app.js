// GymPlanner Pro - Main Application
class App {
    constructor() {
        this.user = null;
        this.exercises = [];
        this.workouts = [];
        this.workoutHistory = [];
        this.currentWorkout = [];
        this.allExercises = [];
        this.templates = [];
        this.isAdmin = false;
        this.execution = {
            workoutId: null,
            workout: null,
            exercises: [],
            currentIndex: 0,
            startedAt: null,
            elapsedSeconds: 0,
            timerInterval: null,
            restInterval: null,
            restSecondsLeft: 0,
            isResting: false,
            setProgress: [], // Array of arrays: [exerciseIndex][setIndex] = {completed: bool, reps: number, notes: string}
            currentSetIndex: 0,
            currentReps: 0
        };
        
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
        
        // Workout execution controls
        document.getElementById('prevExerciseBtn').addEventListener('click', () => this.prevExecutionStep());
        document.getElementById('nextExerciseBtn').addEventListener('click', () => this.nextExecutionStep());
        document.getElementById('completeWorkoutBtn').addEventListener('click', () => this.completeExecution());
        document.getElementById('startRestBtn').addEventListener('click', () => this.startRestTimer());
        
        // Enhanced execution controls
        document.getElementById('repMinus').addEventListener('click', () => this.adjustReps(-1));
        document.getElementById('repPlus').addEventListener('click', () => this.adjustReps(1));
        document.getElementById('completeSetBtn').addEventListener('click', () => this.completeCurrentSet());
    }
    
    // ===== AUTH =====
    async checkAuthStatus() {
        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'status' })
            });
            
            const data = await response.json();
            if (data.success) {
                this.user = data.data;
                this.showApp();
                await this.loadExercises();
                await this.loadTemplates();
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
                credentials: 'same-origin',
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
                credentials: 'same-origin',
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
                credentials: 'same-origin',
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
        this.isAdmin = this.user.role === 'admin';
        this.applyAdminPrivileges();
    }

    applyAdminPrivileges() {
        const templateAdminPanel = document.getElementById('templateAdminPanel');
        const exerciseAdminPanel = document.getElementById('exerciseAdminPanel');

        if (this.isAdmin) {
            templateAdminPanel.classList.remove('hidden');
            exerciseAdminPanel.classList.remove('hidden');
            document.getElementById('adminCreateTemplateBtn').addEventListener('click', () => this.createTemplate());
            document.getElementById('adminCreateExerciseBtn').addEventListener('click', () => this.createExercise());
        } else {
            templateAdminPanel.classList.add('hidden');
            exerciseAdminPanel.classList.add('hidden');
        }
    }

    // ===== EXERCISES =====
    async loadExercises() {
        try {
            const response = await fetch('api/exercises.php', { credentials: 'same-origin' });
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
    
    async loadTemplates() {
        try {
            const response = await fetch('api/templates.php', { credentials: 'same-origin' });
            const data = await response.json();
            
            if (data.success) {
                this.templates = data.data.templates;
                this.displayTemplates();
            }
        } catch (err) {
            console.error('Failed to load templates:', err);
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
                ${this.isAdmin ? `
                    <div class="exercise-actions">
                        <button class="btn btn-secondary exercise-edit-btn" data-id="${exercise.id}">Edit</button>
                        <button class="btn btn-danger exercise-delete-btn" data-id="${exercise.id}">Delete</button>
                    </div>
                ` : ''}
            `;

            if (this.isAdmin) {
                item.querySelector('.exercise-edit-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.editExercise(exercise);
                });

                item.querySelector('.exercise-delete-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.deleteExercise(exercise.id);
                });
            }

            list.appendChild(item);
        });
    }
    
    displayTemplates() {
        const list = document.getElementById('templatesList');
        list.innerHTML = '';
        
        this.templates.forEach(template => {
            const item = document.createElement('div');
            item.className = 'template-item collapsed';
            
            const difficultyClass = `template-difficulty-${template.difficulty.toLowerCase()}`;
            const exerciseCount = template.exercises ? template.exercises.length : 0;
            
            // Create exercise sets display with meaningful names
            let setsHtml = '';
            if (template.exercise_sets) {
                const setNames = this.getSetNamesForTemplate(template);
                let setIndex = 0;
                for (const [setGroup, exercises] of Object.entries(template.exercise_sets)) {
                    const setName = setNames[setIndex] || `Set ${setIndex + 1}`;
                    setsHtml += `<div class="template-set">
                        <div class="set-header">
                            <div class="set-number">${setName}</div>
                            <div class="set-exercise-count">${exercises.length} exercise${exercises.length > 1 ? 's' : ''}</div>
                        </div>
                        <div class="set-exercises">
                            ${exercises.map((ex, exIndex) => `
                                <div class="exercise-detail">
                                    <div class="exercise-icon ${this.getExerciseIconClass(ex.muscle_group)}"></div>
                                    <div class="exercise-info">
                                        <span class="exercise-name">${ex.name}</span>
                                        <span class="exercise-specs">${ex.sets}×${ex.reps} • ${Math.floor(ex.rest_seconds / 60)}:${(ex.rest_seconds % 60).toString().padStart(2, '0')} rest</span>
                                    </div>
                                    ${exIndex < exercises.length - 1 ? '<div class="exercise-divider">→</div>' : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>`;
                    setIndex++;
                }
                setsHtml = `<div class="template-sets">${setsHtml}</div>`;
            }
            
            item.innerHTML = `
                <div class="template-header">
                    <div class="template-name">${template.name}</div>
                    <div class="template-description">${template.description}</div>
                    <div class="template-expand-icon">▼</div>
                </div>
                <div class="template-content">
                    ${setsHtml}
                    <div class="template-footer">
                        <div class="template-meta">
                            <span class="template-exercises">${exerciseCount} exercises</span>
                            <span class="template-difficulty ${difficultyClass}">${template.difficulty}</span>
                            ${template.estimated_duration ? `<span class="template-duration">• ${template.estimated_duration} min</span>` : ''}
                        </div>
                        <div class="template-actions">
                            <button class="btn btn-primary template-use-btn">Use Template</button>
                            ${this.isAdmin ? `<button class="btn btn-secondary template-edit-btn" data-id="${template.id}">Edit</button>
                            <button class="btn btn-danger template-delete-btn" data-id="${template.id}">Delete</button>` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            // Add click handler for expand/collapse
            item.addEventListener('click', (e) => {
                if (!e.target.classList.contains('template-use-btn')) {
                    this.toggleTemplateExpansion(item);
                }
            });
            
            // Add separate handler for use button
            item.querySelector('.template-use-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                this.selectTemplate(template);
            });

            if (this.isAdmin) {
                item.querySelector('.template-edit-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.editTemplate(template);
                });

                item.querySelector('.template-delete-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.deleteTemplate(template.id);
                });
            }
            
            list.appendChild(item);
        });
    }
    
    toggleTemplateExpansion(item) {
        const isCollapsed = item.classList.contains('collapsed');
        const icon = item.querySelector('.template-expand-icon');
        
        if (isCollapsed) {
            item.classList.remove('collapsed');
            item.classList.add('expanded');
            icon.textContent = '▲';
        } else {
            item.classList.remove('expanded');
            item.classList.add('collapsed');
            icon.textContent = '▼';
        }
    }

    async createExercise() {
        const id = document.getElementById('adminExerciseId').value;
        const name = document.getElementById('adminExerciseName').value.trim();
        const muscle_group = document.getElementById('adminExerciseGroup').value.trim();
        const difficulty = document.getElementById('adminExerciseDifficulty').value.trim();
        const instructions = document.getElementById('adminExerciseInstructions').value.trim();

        if (!name || !muscle_group || !difficulty) {
            alert('Please complete required fields (name, group, difficulty).');
            return;
        }

        const payload = { name, muscle_group, difficulty, instructions };
        const method = id ? 'PUT' : 'POST';
        if (id) payload.id = id;

        const response = await fetch('api/exercises.php', {
            method,
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (data.success) {
            await this.loadExercises();
            document.getElementById('adminExerciseId').value = '';
            document.getElementById('adminExerciseName').value = '';
            document.getElementById('adminExerciseGroup').value = '';
            document.getElementById('adminExerciseDifficulty').value = '';
            document.getElementById('adminExerciseInstructions').value = '';
            alert(id ? 'Exercise updated successfully' : 'Exercise created successfully');
        } else {
            alert(data.message || 'Failed to save exercise');
        }
    }

    editExercise(exercise) {
        document.getElementById('adminExerciseId').value = exercise.id;
        document.getElementById('adminExerciseName').value = exercise.name;
        document.getElementById('adminExerciseGroup').value = exercise.muscle_group;
        document.getElementById('adminExerciseDifficulty').value = exercise.difficulty;
        document.getElementById('adminExerciseInstructions').value = exercise.instructions;
        document.getElementById('adminCreateExerciseBtn').textContent = 'Save Exercise';
    }

    async deleteExercise(id) {
        if (!confirm('Delete this exercise?')) return;

        const response = await fetch('api/exercises.php', {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const data = await response.json();
        if (data.success) {
            await this.loadExercises();
            alert('Exercise deleted');
        } else {
            alert(data.message || 'Failed to delete exercise');
        }
    }

    async createTemplate() {
        const id = document.getElementById('adminTemplateId').value;
        const name = document.getElementById('adminTemplateName').value.trim();
        const category = document.getElementById('adminTemplateCategory').value.trim();
        const difficulty = document.getElementById('adminTemplateDifficulty').value.trim();
        const description = document.getElementById('adminTemplateDescription').value.trim();
        const duration = Number(document.getElementById('adminTemplateDuration').value || 0);

        if (!name || !category || !difficulty) {
            alert('Please complete required fields: name, category, difficulty.');
            return;
        }

        const payload = { name, category, difficulty, description, estimated_duration: duration };
        const method = id ? 'PUT' : 'POST';
        if (id) payload.id = id;

        const response = await fetch('api/templates.php', {
            method,
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (data.success) {
            this.templates = data.data.templates;
            this.displayTemplates();
            document.getElementById('adminTemplateId').value = '';
            document.getElementById('adminTemplateName').value = '';
            document.getElementById('adminTemplateCategory').value = '';
            document.getElementById('adminTemplateDifficulty').value = '';
            document.getElementById('adminTemplateDescription').value = '';
            document.getElementById('adminTemplateDuration').value = '';
            document.getElementById('adminCreateTemplateBtn').textContent = 'Create Template';
            alert(id ? 'Template updated successfully' : 'Template created successfully');
        } else {
            alert(data.message || 'Failed to create template');
        }
    }

    editTemplate(template) {
        document.getElementById('adminTemplateId').value = template.id;
        document.getElementById('adminTemplateName').value = template.name;
        document.getElementById('adminTemplateCategory').value = template.category;
        document.getElementById('adminTemplateDifficulty').value = template.difficulty;
        document.getElementById('adminTemplateDescription').value = template.description || '';
        document.getElementById('adminTemplateDuration').value = template.estimated_duration || '';
        document.getElementById('adminCreateTemplateBtn').textContent = 'Save Template';
    }

    async deleteTemplate(id) {
        if (!confirm('Delete this template?')) return;

        const response = await fetch('api/templates.php', {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const data = await response.json();
        if (data.success) {
            this.templates = data.data.templates;
            this.displayTemplates();
            alert('Template deleted');
        } else {
            alert(data.message || 'Failed to delete template');
        }
    }

    getSetNamesForTemplate(template) {
        const templateName = template.name.toLowerCase();
        
        if (templateName.includes('bro split')) {
            if (templateName.includes('chest')) {
                return ['Chest Exercises', 'Arm Balance'];
            } else if (templateName.includes('back')) {
                return ['Back Exercises'];
            } else if (templateName.includes('leg')) {
                return ['Leg Exercises'];
            } else if (templateName.includes('shoulder')) {
                return ['Shoulder Exercises', 'Core'];
            } else if (templateName.includes('arm')) {
                return ['Biceps', 'Triceps'];
            }
        } else if (templateName.includes('push/pull/legs')) {
            if (templateName.includes('push')) {
                return ['Push Exercises'];
            } else if (templateName.includes('pull')) {
                return ['Pull Exercises'];
            } else if (templateName.includes('legs')) {
                return ['Leg Exercises'];
            }
        } else if (templateName.includes('upper/lower')) {
            if (templateName.includes('upper')) {
                return ['Upper Body'];
            } else if (templateName.includes('lower')) {
                return ['Lower Body'];
            }
        } else if (templateName.includes('4-day')) {
            if (templateName.includes('chest') || templateName.includes('day 1')) {
                return ['Chest', 'Triceps'];
            } else if (templateName.includes('back') || templateName.includes('day 2')) {
                return ['Back', 'Biceps'];
            } else if (templateName.includes('day 3')) {
                return ['Legs'];
            } else if (templateName.includes('shoulders') || templateName.includes('day 4')) {
                return ['Shoulders', 'Core'];
            }
        } else if (templateName.includes('full body')) {
            return ['Full Body Circuit'];
        }
        
        // Default fallback
        return Object.keys(template.exercise_sets || {}).map((_, i) => `Set ${i + 1}`);
    }
    
    getExerciseIconClass(muscleGroup) {
        const iconMap = {
            'Chest': 'chest-icon',
            'Back': 'back-icon',
            'Legs': 'legs-icon',
            'Shoulders': 'shoulders-icon',
            'Biceps': 'biceps-icon',
            'Triceps': 'triceps-icon',
            'Core': 'core-icon',
            'Calves': 'calves-icon'
        };
        return iconMap[muscleGroup] || 'default-icon';
    }
    
    selectTemplate(template) {
        if (confirm(`Use the "${template.name}" template? This will load all exercises into the workout builder.`)) {
            // Switch to builder page
            this.switchPage('builder');
            
            // Clear current builder
            document.getElementById('workoutName').value = template.name;
            document.getElementById('workoutDesc').value = template.description || '';
            
            // Clear existing exercises in builder
            this.currentWorkout = [];
            
            // Add template exercises to builder
            if (template.exercises && template.exercises.length > 0) {
                template.exercises.forEach((ex) => {
                    this.addTemplateExerciseToBuilder(ex);
                });
            }
            
            // Update the preview
            this.updateWorkoutPreview();
        }
    }
    
    addTemplateExerciseToBuilder(exercise) {
        this.currentWorkout.push({
            exercise_id: exercise.exercise_id,
            name: exercise.name,
            sets: exercise.sets,
            reps: exercise.reps,
            rest_seconds: exercise.rest_seconds
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
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.workouts = data.data.workouts;
                this.displayUserWorkouts();
                await this.loadWorkoutHistory();
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
    
    async loadWorkoutHistory() {
        try {
            const response = await fetch('api/workouts.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_history' })
            });
            const data = await response.json();

            if (data.success) {
                this.workoutHistory = data.data.history;
                this.displayWorkoutHistory();
            }
        } catch (err) {
            console.error('Failed to load workout history:', err);
        }
    }

    displayWorkoutHistory() {
        const list = document.getElementById('historyList');
        list.innerHTML = '';

        if (!this.workoutHistory || this.workoutHistory.length === 0) {
            list.innerHTML = '<p style="color: var(--text-light); text-align: center; padding: 40px 0;">No workout history yet.</p>';
            return;
        }

        this.workoutHistory.forEach(item => {
            const card = document.createElement('div');
            card.className = 'history-card';

            const date = new Date(item.completed_at).toLocaleString();
            card.innerHTML = `
                <h4>${item.workout_name}</h4>
                <div class="history-meta">Completed: ${date} · Duration: ${item.duration_minutes} min</div>
                <div class="history-notes">${item.notes || 'No notes provided'}</div>
            `;

            list.appendChild(card);
        });
    }

    async startWorkout(workoutId) {
        try {
            const response = await fetch('api/workouts.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_detail', workout_id: workoutId })
            });

            const data = await response.json();
            if (data.success) {
                this.execution.workout = data.data.workout;
                this.execution.workoutId = workoutId;
                this.execution.exercises = data.data.workout.exercises;
                this.execution.currentIndex = 0;
                this.execution.elapsedSeconds = 0;
                this.execution.startedAt = Date.now();
                this.execution.isResting = false;
                this.execution.restSecondsLeft = 0;
                
                // Initialize set progress tracking
                this.execution.setProgress = this.execution.exercises.map(exercise => 
                    Array.from({length: exercise.sets}, () => ({ completed: false, reps: 0, notes: '' }))
                );
                this.execution.currentSetIndex = 0;
                this.execution.currentReps = 0;

                this.startDurationTimer();
                this.renderExecutionPage();
            } else {
                alert('Failed to fetch workout details: ' + data.message);
            }
        } catch (err) {
            console.error('Failed to start workout:', err);
        }
    }

    renderExecutionPage() {
        this.switchPage('execution');

        const workout = this.execution.workout;
        document.getElementById('executionWorkoutName').textContent = workout.name;
        document.getElementById('executionWorkoutDesc').textContent = workout.description || 'No description';

        // Update progress
        const totalExercises = this.execution.exercises.length;
        const completedExercises = this.execution.currentIndex;
        const progressPercent = totalExercises > 0 ? (completedExercises / totalExercises) * 100 : 0;
        
        document.getElementById('progressFill').style.width = `${progressPercent}%`;
        document.getElementById('progressText').textContent = `${completedExercises}/${totalExercises} exercises`;

        // Show current exercise details
        this.renderCurrentExercise();

        // Show all exercises overview
        const steps = document.getElementById('executionSteps');
        steps.innerHTML = '';

        this.execution.exercises.forEach((ex, index) => {
            const step = document.createElement('div');
            const isActive = index === this.execution.currentIndex;
            const isCompleted = index < this.execution.currentIndex;
            
            step.className = `execution-step${isActive ? ' active' : ''}${isCompleted ? ' completed' : ''}`;
            step.innerHTML = `
                <span>${index + 1}. ${ex.name} (${ex.sets}x${ex.reps})</span>
                <span>${ex.rest_seconds}s rest</span>
            `;
            steps.appendChild(step);
        });

        this.updateExecutionProgress();
    }

    renderCurrentExercise() {
        const current = this.execution.exercises[this.execution.currentIndex];
        if (!current) return;

        document.getElementById('currentExerciseName').textContent = current.name;
        document.getElementById('currentExerciseSets').textContent = `${current.sets} sets`;
        document.getElementById('currentExerciseReps').textContent = `${current.reps} reps`;
        document.getElementById('currentExerciseRest').textContent = `${current.rest_seconds}s rest`;

        // Render set tracker
        const setList = document.getElementById('setList');
        setList.innerHTML = '';

        for (let i = 0; i < current.sets; i++) {
            const setItem = document.createElement('div');
            const setData = this.execution.setProgress[this.execution.currentIndex][i];
            
            setItem.className = `set-item${setData.completed ? ' completed' : ''}${i === this.execution.currentSetIndex ? ' active' : ''}`;
            setItem.textContent = i + 1;
            setItem.addEventListener('click', () => this.selectSet(i));
            setList.appendChild(setItem);
        }

        // Update rep counter
        this.updateRepCounter();
        
        // Show exercise notes
        document.getElementById('exerciseNotes').value = this.execution.setProgress[this.execution.currentIndex][this.execution.currentSetIndex].notes || '';
    }

    selectSet(setIndex) {
        this.execution.currentSetIndex = setIndex;
        const setData = this.execution.setProgress[this.execution.currentIndex][setIndex];
        this.execution.currentReps = setData.reps;
        this.updateRepCounter();
        this.renderCurrentExercise();
    }

    adjustReps(delta) {
        this.execution.currentReps = Math.max(0, this.execution.currentReps + delta);
        this.updateRepCounter();
    }

    updateRepCounter() {
        document.getElementById('repCount').textContent = this.execution.currentReps;
    }

    completeCurrentSet() {
        const currentExercise = this.execution.exercises[this.execution.currentIndex];
        const setData = this.execution.setProgress[this.execution.currentIndex][this.execution.currentSetIndex];
        
        // Save current reps and notes
        setData.reps = this.execution.currentReps;
        setData.notes = document.getElementById('exerciseNotes').value;
        setData.completed = true;

        // Move to next set or next exercise
        if (this.execution.currentSetIndex < currentExercise.sets - 1) {
            this.execution.currentSetIndex++;
            this.execution.currentReps = 0;
        } else {
            // All sets completed for this exercise
            this.nextExecutionStep();
            return;
        }

        this.renderCurrentExercise();
        this.updateRepCounter();
    }

    startDurationTimer() {
        if (this.execution.timerInterval) {
            clearInterval(this.execution.timerInterval);
        }

        this.execution.timerInterval = setInterval(() => {
            this.execution.elapsedSeconds += 1;
            const minutes = String(Math.floor(this.execution.elapsedSeconds / 60)).padStart(2, '0');
            const seconds = String(this.execution.elapsedSeconds % 60).padStart(2, '0');
            document.getElementById('executionTimer').textContent = `${minutes}:${seconds}`;
        }, 1000);
    }

    updateExecutionProgress() {
        document.querySelectorAll('.execution-step').forEach((step, index) => {
            step.classList.toggle('active', index === this.execution.currentIndex);
        });

        const current = this.execution.exercises[this.execution.currentIndex];
        document.getElementById('restCountdown').textContent = this.execution.isResting
            ? `Rest: ${this.execution.restSecondsLeft}s`
            : `Next rest: ${current ? current.rest_seconds : 0}s`;
    }

    nextExecutionStep() {
        if (this.execution.currentIndex < this.execution.exercises.length - 1) {
            this.execution.currentIndex += 1;
            this.execution.currentSetIndex = 0;
            this.execution.currentReps = 0;
            this.execution.isResting = false;
            this.execution.restSecondsLeft = 0;
            this.renderExecutionPage();
        }
    }

    prevExecutionStep() {
        if (this.execution.currentIndex > 0) {
            this.execution.currentIndex -= 1;
            this.execution.currentSetIndex = 0;
            this.execution.currentReps = 0;
            this.execution.isResting = false;
            this.execution.restSecondsLeft = 0;
            this.renderExecutionPage();
        }
    }

    startRestTimer() {
        if (this.execution.restInterval) {
            clearInterval(this.execution.restInterval);
        }

        const current = this.execution.exercises[this.execution.currentIndex];
        if (!current) return;

        this.execution.isResting = true;
        this.execution.restSecondsLeft = current.rest_seconds;
        document.getElementById('restCountdown').textContent = `Rest: ${this.execution.restSecondsLeft}s`;

        this.execution.restInterval = setInterval(() => {
            this.execution.restSecondsLeft -= 1;
            if (this.execution.restSecondsLeft <= 0) {
                clearInterval(this.execution.restInterval);
                this.execution.isResting = false;
                this.updateExecutionProgress();
                alert('Rest complete!');
            } else {
                document.getElementById('restCountdown').textContent = `Rest: ${this.execution.restSecondsLeft}s`;
            }
        }, 1000);
    }

    async completeExecution() {
        if (this.execution.timerInterval) {
            clearInterval(this.execution.timerInterval);
            this.execution.timerInterval = null;
        }
        if (this.execution.restInterval) {
            clearInterval(this.execution.restInterval);
            this.execution.restInterval = null;
        }

        const durationMinutes = Math.max(1, Math.ceil(this.execution.elapsedSeconds / 60));

        try {
            const response = await fetch('api/workouts.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'complete',
                    workout_id: this.execution.workoutId,
                    duration_minutes: durationMinutes,
                    notes: 'Completed via enhanced execution UI',
                    execution_data: this.execution.setProgress
                })
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Workout complete! History saved.');
                this.loadWorkoutHistory();
                this.switchPage('history');
            } else {
                alert('Failed to save workout completion: ' + data.message);
            }
        } catch (err) {
            console.error('Failed to complete workout:', err);
        }
    }
    
    updateDashboard() {
        const stats = document.querySelectorAll('.stat-value');
        stats[0].textContent = this.workouts.length;

        const thisWeekCount = this.workoutHistory.filter(item => {
            const completed = new Date(item.completed_at);
            const startOfWeek = new Date();
            startOfWeek.setHours(0, 0, 0, 0);
            startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
            return completed >= startOfWeek;
        }).length;

        stats[1].textContent = thisWeekCount;
        stats[2].textContent = this.allExercises.length;
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
