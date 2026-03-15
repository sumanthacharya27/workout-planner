// Custom Workouts & Workout Execution module
const CustomWorkouts = {
    
    currentExercises: [],
    
    async load() {
        await this.loadSaved();
        this.setupForm();
    },
    
    setupForm() {
        document.getElementById('addExerciseBtn').addEventListener('click', () => {
            this.openExerciseModal();
        });
        
        document.getElementById('saveWorkout').addEventListener('click', () => {
            this.saveWorkout();
        });
        
        document.getElementById('cancelWorkout').addEventListener('click', () => {
            this.clearForm();
        });
        
        document.querySelector('.close').addEventListener('click', () => {
            this.closeExerciseModal();
        });
        
        document.getElementById('saveExercise').addEventListener('click', () => {
            this.addExercise();
        });
    },
    
    openExerciseModal() {
        document.getElementById('exerciseModal').style.display = 'block';
    },
    
    closeExerciseModal() {
        document.getElementById('exerciseModal').style.display = 'none';
        this.clearExerciseForm();
    },
    
    addExercise() {
        const exercise = {
            name: document.getElementById('exerciseName').value.trim(),
            sets: parseInt(document.getElementById('exerciseSets').value),
            reps: parseInt(document.getElementById('exerciseReps').value),
            weight: parseFloat(document.getElementById('exerciseWeight').value) || 0,
            rest: parseInt(document.getElementById('exerciseRest').value),
            notes: document.getElementById('exerciseNotes').value.trim()
        };
        
        if (!exercise.name) {
            alert('Please enter exercise name');
            return;
        }
        
        this.currentExercises.push(exercise);
        this.renderExercises();
        this.closeExerciseModal();
    },
    
    renderExercises() {
        const container = document.getElementById('exercisesList');
        
        if (this.currentExercises.length === 0) {
            container.innerHTML = '<p class="empty-state">No exercises added yet.</p>';
            return;
        }
        
        container.innerHTML = this.currentExercises.map((ex, index) => `
            <div class="exercise-item">
                <div class="exercise-info">
                    <div class="exercise-name">${ex.name}</div>
                    <div class="exercise-details">
                        ${ex.sets} sets × ${ex.reps} reps
                        ${ex.weight > 0 ? `• ${ex.weight}kg` : ''}
                        • ${ex.rest}s rest
                    </div>
                </div>
                <button class="btn-icon-small" onclick="CustomWorkouts.removeExercise(${index})">🗑️</button>
            </div>
        `).join('');
    },
    
    removeExercise(index) {
        this.currentExercises.splice(index, 1);
        this.renderExercises();
    },
    
    async saveWorkout() {
        const name = document.getElementById('workoutName').value.trim();
        const description = document.getElementById('workoutDescription').value.trim();
        
        if (!name) {
            alert('Please enter workout name');
            return;
        }
        
        if (this.currentExercises.length === 0) {
            alert('Please add at least one exercise');
            return;
        }
        
        const workout = {
            name,
            description,
            exercises: this.currentExercises
        };
        
        const result = await Database.saveWorkout(workout);
        
        if (result.success) {
            alert('Workout saved successfully!');
            this.clearForm();
            await this.loadSaved();
        } else {
            alert(result.message || 'Failed to save workout');
        }
    },
    
    async loadSaved() {
        const result = await Database.getWorkouts();
        const container = document.getElementById('savedWorkoutsList');
        
        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(workout => `
                <div class="saved-item">
                    <div class="saved-item-header">
                        <div class="saved-item-name">${workout.plan_name}</div>
                        <button class="btn-icon-small" onclick="CustomWorkouts.deleteWorkout(${workout.plan_id})">🗑️</button>
                    </div>
                    <div class="saved-item-info">
                        ${workout.description || 'No description'}<br>
                        ${workout.exercise_count} exercises
                    </div>
                    <button class="btn-small primary" onclick="CustomWorkouts.startWorkout(${workout.plan_id})">Start Workout</button>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="empty-state">No custom workouts yet. Create your first one!</p>';
        }
    },
    
    async deleteWorkout(planId) {
        if (!confirm('Delete this workout?')) return;
        
        const result = await Database.deleteWorkout(planId);
        if (result.success) {
            await this.loadSaved();
        } else {
            alert('Failed to delete workout');
        }
    },
    
    async startWorkout(planId) {
        const result = await Database.getWorkout(planId);
        if (result.success && result.data) {
            WorkoutExecution.start(result.data);
        }
    },
    
    clearForm() {
        document.getElementById('workoutName').value = '';
        document.getElementById('workoutDescription').value = '';
        this.currentExercises = [];
        this.renderExercises();
    },
    
    clearExerciseForm() {
        document.getElementById('exerciseName').value = '';
        document.getElementById('exerciseSets').value = 3;
        document.getElementById('exerciseReps').value = 10;
        document.getElementById('exerciseWeight').value = '';
        document.getElementById('exerciseRest').value = 60;
        document.getElementById('exerciseNotes').value = '';
    }
};

// Workout Execution module
const WorkoutExecution = {
    
    currentWorkout: null,
    currentIndex: 0,
    startTime: null,
    
    start(workout) {
        this.currentWorkout = workout;
        this.currentIndex = 0;
        this.startTime = Date.now();
        
        // Initialize tracking
        this.currentWorkout.exercises.forEach(ex => {
            ex.completedSets = 0;
            ex.setCheckboxes = new Array(ex.sets).fill(false);
        });
        
        Navigation.navigateTo('execute');
        this.displayExercise();
        this.setupControls();
    },
    
    setupControls() {
        document.getElementById('backToWorkouts').onclick = () => {
            if (confirm('Quit workout? Progress will not be saved.')) {
                Navigation.navigateTo('workouts');
            }
        };
        
        document.getElementById('nextExercise').onclick = () => this.nextExercise();
        document.getElementById('prevExercise').onclick = () => this.prevExercise();
        document.getElementById('quitWorkout').onclick = () => {
            if (confirm('Quit workout?')) {
                Navigation.navigateTo('workouts');
            }
        };
        document.getElementById('completeWorkout').onclick = () => this.complete();
    },
    
    displayExercise() {
        const exercise = this.currentWorkout.exercises[this.currentIndex];
        const container = document.getElementById('exerciseDisplay');
        
        document.getElementById('executeWorkoutName').textContent = this.currentWorkout.name || this.currentWorkout.plan_name;
        document.getElementById('currentExerciseNum').textContent = this.currentIndex + 1;
        document.getElementById('totalExercisesNum').textContent = this.currentWorkout.exercises.length;
        
        const progress = ((this.currentIndex + 1) / this.currentWorkout.exercises.length) * 100;
        document.getElementById('workoutProgressBar').style.width = progress + '%';
        
        container.innerHTML = `
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
                    <div class="stat-name">Rest (s)</div>
                </div>
            </div>
            <div class="sets-tracker">
                <h3>Complete Sets</h3>
                <div class="sets-grid">
                    ${Array.from({length: exercise.sets}, (_, i) => `
                        <label class="set-checkbox ${exercise.setCheckboxes[i] ? 'completed' : ''}">
                            <input type="checkbox" ${exercise.setCheckboxes[i] ? 'checked' : ''} onchange="WorkoutExecution.toggleSet(${i})">
                            Set ${i + 1}
                        </label>
                    `).join('')}
                </div>
            </div>
        `;
        
        const isLast = this.currentIndex === this.currentWorkout.exercises.length - 1;
        document.getElementById('prevExercise').style.display = this.currentIndex > 0 ? 'block' : 'none';
        document.getElementById('nextExercise').style.display = !isLast ? 'block' : 'none';
        document.getElementById('completeWorkout').style.display = isLast ? 'block' : 'none';
    },
    
    toggleSet(setIndex) {
        const exercise = this.currentWorkout.exercises[this.currentIndex];
        exercise.setCheckboxes[setIndex] = !exercise.setCheckboxes[setIndex];
        
        if (exercise.setCheckboxes[setIndex]) {
            exercise.completedSets++;
        } else {
            exercise.completedSets--;
        }
        
        this.displayExercise();
    },
    
    nextExercise() {
        if (this.currentIndex < this.currentWorkout.exercises.length - 1) {
            this.currentIndex++;
            this.displayExercise();
        }
    },
    
    prevExercise() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.displayExercise();
        }
    },
    
    async complete() {
        const duration = Math.floor((Date.now() - this.startTime) / 1000 / 60);
        
        const workoutData = {
            workout_name: this.currentWorkout.name || this.currentWorkout.plan_name,
            duration: duration,
            exercises: this.currentWorkout.exercises.map(ex => ({
                name: ex.name,
                sets: ex.sets,
                reps: ex.reps,
                weight: ex.weight,
                completedSets: ex.completedSets
            }))
        };
        
        const result = await Database.saveWorkoutLog(workoutData);
        
        if (result.success) {
            await Database.updateStats(duration, this.currentWorkout.exercises.length);
            alert(`Workout completed! 🎉\n\nDuration: ${duration} minutes`);
            Navigation.navigateTo('dashboard');
        } else {
            alert('Error saving workout: ' + (result.message || 'Unknown error'));
        }
    }
};