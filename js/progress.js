// ========================================
// PROGRESS MODULE
// ========================================

// Add progress entry
async function addProgress(progressData) {
    const formData = new FormData();
    for (let key in progressData) {
        formData.append(key, progressData[key]);
    }
    
    const response = await fetch(ENDPOINTS.PROGRESS + '?action=add', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Get all progress entries
async function getAllProgress() {
    const response = await fetch(ENDPOINTS.PROGRESS + '?action=getAll');
    return await response.json();
}

// Get latest weight
async function getLatestWeight() {
    const response = await fetch(ENDPOINTS.PROGRESS + '?action=getLatestWeight');
    return await response.json();
}

// Get weight history for charts
async function getWeightHistory(limit = 30) {
    const response = await fetch(ENDPOINTS.PROGRESS + `?action=getWeightHistory&limit=${limit}`);
    return await response.json();
}

// Delete progress entry
async function deleteProgress(progressId) {
    const formData = new FormData();
    formData.append('progress_id', progressId);
    
    const response = await fetch(ENDPOINTS.PROGRESS + '?action=delete', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Get user stats
async function getUserStats() {
    const response = await fetch(ENDPOINTS.PROGRESS + '?action=getStats');
    return await response.json();
}

// Update stats after workout
async function updateUserStats(duration, exercisesCount) {
    const formData = new FormData();
    formData.append('duration', duration);
    formData.append('exercises_count', exercisesCount);
    
    const response = await fetch(ENDPOINTS.PROGRESS + '?action=updateStats', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Display progress list
function displayProgress(progressList) {
    const container = document.getElementById('progressList');
    
    if (!container) return;
    
    if (progressList.length === 0) {
        container.innerHTML = '<p class="empty-state">No progress entries yet. Add your first one!</p>';
        return;
    }
    
    container.innerHTML = progressList.map(entry => `
        <div class="progress-item">
            <div class="progress-date">${formatDate(entry.tracking_date)}</div>
            <div class="progress-data">
                ${entry.weight > 0 ? `<span>Weight: ${entry.weight}kg</span>` : ''}
                ${entry.chest > 0 ? `<span>Chest: ${entry.chest}cm</span>` : ''}
                ${entry.waist > 0 ? `<span>Waist: ${entry.waist}cm</span>` : ''}
                ${entry.hips > 0 ? `<span>Hips: ${entry.hips}cm</span>` : ''}
                ${entry.thighs > 0 ? `<span>Thighs: ${entry.thighs}cm</span>` : ''}
                ${entry.biceps > 0 ? `<span>Biceps: ${entry.biceps}cm</span>` : ''}
            </div>
            ${entry.notes ? `<div class="progress-notes">${entry.notes}</div>` : ''}
            <button class="btn-delete-sm" onclick="confirmDeleteProgress(${entry.progress_id})">Delete</button>
        </div>
    `).join('');
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Confirm delete progress
async function confirmDeleteProgress(progressId) {
    if (confirm('Delete this progress entry?')) {
        const result = await deleteProgress(progressId);
        if (result.success) {
            loadAllProgress();
        } else {
            alert(result.message);
        }
    }
}

// Load all progress
async function loadAllProgress() {
    const result = await getAllProgress();
    if (result.success) {
        displayProgress(result.data);
    }
}

// Create weight chart
async function createWeightChart() {
    const result = await getWeightHistory(30);
    
    if (!result.success || result.data.length === 0) {
        return;
    }
    
    const canvas = document.getElementById('weightChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    const labels = result.data.map(entry => formatDate(entry.tracking_date));
    const weights = result.data.map(entry => entry.weight);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Weight (kg)',
                data: weights,
                borderColor: '#FF6B35',
                backgroundColor: 'rgba(255, 107, 53, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
}