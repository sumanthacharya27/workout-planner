// History module
const History = {
    
    async load(filter = 'all') {
        const result = await Database.getHistory(filter);
        const container = document.getElementById('historyList');
        
        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(workout => `
                <div class="history-item">
                    <div class="history-header">
                        <div class="history-title">${workout.workout_name}</div>
                        <div class="history-date">${this.formatDate(workout.workout_date)}</div>
                    </div>
                    <div class="history-stats">
                        <span>📝 ${workout.exercise_count} exercises</span>
                        <span>⏱️ ${workout.duration} min</span>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="empty-state">No workout history yet. Complete your first workout!</p>';
        }
        
        this.setupFilters();
    },
    
    setupFilters() {
        document.querySelectorAll('#history .filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#history .filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');
                History.load(filter);
            });
        });
    },
    
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
};