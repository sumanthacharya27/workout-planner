// API Configuration
const CONFIG = {
    API_URL: 'http://localhost/gym-planner/php/',
    
    // Pre-made workouts (keep as before)
    PRE_MADE_WORKOUTS: [ /* ... same as before ... */ ]
};

// Get current user ID from session
async function getCurrentUserId() {
    const response = await fetch(CONFIG.API_URL + 'auth.php?action=check', {
        method: 'POST'
    });
    const result = await response.json();
    
    if (result.success && result.data) {
        return result.data.user_id;
    }
    
    // Not logged in - redirect to login
    window.location.href = 'login.html';
    return null;
}