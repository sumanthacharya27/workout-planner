// Make API calls
async function apiCall(url, data = {}) {
    try {
        const formData = new FormData();
        
        for (let key in data) {
            formData.append(key, data[key]);
        }
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        return result;
        
    } catch (error) {
        console.error('API Error:', error);
        return {
            success: false,
            message: 'Network error. Please try again.'
        };
    }
}

// Check if user is logged in
async function checkAuth() {
    const result = await apiCall(ENDPOINTS.CHECK_SESSION);
    return result.success;
}