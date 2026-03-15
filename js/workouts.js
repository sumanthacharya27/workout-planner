// Workouts module - pre-made workouts
const Workouts = {
    
    loadPreMade(filter = 'all') {
        const workoutsList = document.getElementById('workoutsList');
        
        let filtered = CONFIG.PRE_MADE_WORKOUTS;
        if (filter !== 'all') {
            filtered = CONFIG.PRE_MADE_WORKOUTS.filter(w => w.difficulty === filter);
        }
        
        workoutsList.innerHTML = filtered.map(workout => `
            <div class="workout-card" onclick="Workouts.start('${workout.id}')">
                <div class="workout-card-header">
                    <div class="workout-card-title">${workout.name}</div>
                    <span class="workout-badge ${workout.difficulty}">${workout.difficulty}</span>
                </div>
                <div class="workout-card-description">${workout.description}</div>
                <div class="workout-card-info">
                    <span>📝 ${workout.exercises.length} exercises</span>
                    <span>⏱️ ~${this.estimateDuration(workout.exercises)} min</span>
                </div>
            </div>
        `).join('');
        
        this.setupFilters();
    },
    
    setupFilters() {
        document.querySelectorAll('#workouts .filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#workouts .filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');
                Workouts.loadPreMade(filter);
            });
        });
    },
    
    estimateDuration(exercises) {
        let totalSeconds = 0;
        exercises.forEach(ex => {
            totalSeconds += (ex.sets * ex.reps * 3) + (ex.sets * ex.rest);
        });
        return Math.round(totalSeconds / 60);
    },
    
    start(workoutId) {
        const workout = CONFIG.PRE_MADE_WORKOUTS.find(w => w.id === workoutId);
        if (workout) {
            WorkoutExecution.start(workout);
        }
    }
};