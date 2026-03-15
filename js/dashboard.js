// Dashboard module
const Dashboard = {
    
    async load() {
        await this.loadStats();
        await this.loadRecentWorkouts();
    },
    
    async loadStats() {
        const result = await Database.getStats();
        
        if (result.success && result.data) {
            document.getElementById('totalWorkouts').textContent = result.data.total_workouts || 0;
            document.getElementById('currentStreak').textContent = result.data.current_streak || 0;
            document.getElementById('totalTime').textContent = Math.floor((result.data.total_time || 0) / 60);
            document.getElementById('totalExercises').textContent = result.data.total_exercises || 0;
        }
    },
    
    async loadRecentWorkouts() {
        const result = await Database.getHistory('all', 5);
        const container = document.getElementById('recentWorkoutsList');
        
        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(workout => `
                <div class="recent-item">
                    <div class="recent-item-header">
                        <div class="recent-item-name">${workout.workout_name}</div>
                        <div class="recent-item-date">${this.formatDate(workout.workout_date)}</div>
                    </div>
                    <div class="recent-item-stats">
                        ${workout.exercise_count} exercises • ${workout.duration} min
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="empty-state">No workouts yet. Start your first workout!</p>';
        }
    },
    
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
};