// Charts module
const Charts = {
    
    async renderFrequencyChart() {
        const ctx = document.getElementById('frequencyChart');
        if (!ctx) return;
        
        const result = await Database.getHistory('week');
        
        // Get last 7 days
        const days = [];
        const counts = [0, 0, 0, 0, 0, 0, 0];
        
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            days.push(date.toLocaleDateString('en-US', { weekday: 'short' }));
        }
        
        // Count workouts per day (simplified)
        if (result.success && result.data) {
            result.data.forEach(workout => {
                const workoutDate = new Date(workout.workout_date);
                const dayIndex = workoutDate.getDay();
                counts[dayIndex]++;
            });
        }
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Workouts',
                    data: counts,
                    backgroundColor: 'rgba(255, 107, 53, 0.8)',
                    borderColor: 'rgba(255, 107, 53, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    },
    
    async renderExerciseChart() {
        const ctx = document.getElementById('exerciseChart');
        if (!ctx) return;
        
        const result = await Database.getHistory('all');
        
        if (!result.success || !result.data || result.data.length === 0) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['No data yet'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#E0E0E0']
                    }]
                }
            });
            return;
        }
        
        // Count workout types
        const workoutCounts = {};
        result.data.forEach(workout => {
            const name = workout.workout_name;
            workoutCounts[name] = (workoutCounts[name] || 0) + 1;
        });
        
        // Get top 5
        const sorted = Object.entries(workoutCounts)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5);
        
        const labels = sorted.map(e => e[0]);
        const data = sorted.map(e => e[1]);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(255, 107, 53, 0.8)',
                        'rgba(247, 147, 30, 0.8)',
                        'rgba(6, 214, 160, 0.8)',
                        'rgba(239, 71, 111, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    }
};