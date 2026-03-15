// Progress module
const Progress = {
    
    async load() {
        await this.loadStats();
        await this.loadCharts();
        await this.loadAchievements();
    },
    
    async loadStats() {
        const result = await Database.getStats();
        // Stats displayed on dashboard
    },
    
    async loadCharts() {
        await Charts.renderFrequencyChart();
        await Charts.renderExerciseChart();
    },
    
    async loadAchievements() {
        const result = await Database.getStats();
        
        if (result.success && result.data) {
            const stats = result.data;
            const achievements = document.querySelectorAll('.achievement-card');
            
            // First Workout (1 workout)
            if (stats.total_workouts >= 1) {
                achievements[0].classList.remove('locked');
                achievements[0].classList.add('unlocked');
            }
            
            // Week Warrior (7 day streak)
            if (stats.current_streak >= 7) {
                achievements[1].classList.remove('locked');
                achievements[1].classList.add('unlocked');
            }
            
            // Century Club (100 workouts)
            if (stats.total_workouts >= 100) {
                achievements[2].classList.remove('locked');
                achievements[2].classList.add('unlocked');
            }
            
            // Consistency King (30 day streak)
            if (stats.current_streak >= 30) {
                achievements[3].classList.remove('locked');
                achievements[3].classList.add('unlocked');
            }
        }
    }
};