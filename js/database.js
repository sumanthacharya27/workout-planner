const Database = {
    
    async query(endpoint, data = {}) {
        try {
            const formData = new FormData();
            // Don't add user_id - it comes from session now
            
            for (let key in data) {
                if (typeof data[key] === 'object') {
                    formData.append(key, JSON.stringify(data[key]));
                } else {
                    formData.append(key, data[key]);
                }
            }
            
            const response = await fetch(CONFIG.API_URL + endpoint, {
                method: 'POST',
                body: formData
            });
            
            return await response.json();
        } catch (error) {
            console.error('Database error:', error);
            return { success: false, message: 'Connection error' };
        }
    },
    
    // Rest same as before...
};