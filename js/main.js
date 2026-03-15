// Main initialization
document.addEventListener('DOMContentLoaded', async function() {
    
    // Check if user is logged in
    const response = await fetch(CONFIG.API_URL + 'auth.php?action=check', {
        method: 'POST'
    });
    const result = await response.json();
    
    if (!result.success) {
        // Not logged in - redirect
        window.location.href = 'login.html';
        return;
    }
    
    // Display user name in navbar (add this to your index.html navbar)
    const userName = result.data.full_name;
    document.getElementById('userName').textContent = userName;
    
    // Initialize navigation
    Navigation.init();
    
    // Load dashboard
    Dashboard.load();
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('exerciseModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
});

// Logout function
function logout() {
    fetch(CONFIG.API_URL + 'auth.php?action=logout', { method: 'POST' })
        .then(() => {
            window.location.href = 'login.html';
        });
}