// Exercise Database
const exercises = {
    beginner: {
        strength: [
            { name: 'Push-ups', sets: 3, reps: '8-10' },
            { name: 'Bodyweight Squats', sets: 3, reps: '12-15' },
            { name: 'Plank', sets: 3, reps: '30 seconds' },
            { name: 'Lunges', sets: 3, reps: '10 each leg' },
            { name: 'Dumbbell Rows', sets: 3, reps: '10-12' },
            { name: 'Glute Bridges', sets: 3, reps: '12-15' }
        ],
        muscle: [
            { name: 'Dumbbell Chest Press', sets: 3, reps: '10-12' },
            { name: 'Lat Pulldown', sets: 3, reps: '10-12' },
            { name: 'Leg Press', sets: 3, reps: '12-15' },
            { name: 'Shoulder Press', sets: 3, reps: '10-12' },
            { name: 'Bicep Curls', sets: 3, reps: '12-15' },
            { name: 'Tricep Extensions', sets: 3, reps: '12-15' }
        ],
        endurance: [
            { name: 'Jumping Jacks', sets: 3, reps: '30 seconds' },
            { name: 'Mountain Climbers', sets: 3, reps: '30 seconds' },
            { name: 'Burpees', sets: 3, reps: '10' },
            { name: 'High Knees', sets: 3, reps: '30 seconds' },
            { name: 'Jump Rope', sets: 3, reps: '45 seconds' },
            { name: 'Box Steps', sets: 3, reps: '12 each leg' }
        ]
    },
    intermediate: {
        strength: [
            { name: 'Barbell Squats', sets: 4, reps: '6-8' },
            { name: 'Barbell Bench Press', sets: 4, reps: '6-8' },
            { name: 'Deadlifts', sets: 4, reps: '5-6' },
            { name: 'Overhead Press', sets: 4, reps: '6-8' },
            { name: 'Barbell Rows', sets: 4, reps: '8-10' },
            { name: 'Romanian Deadlifts', sets: 3, reps: '10-12' }
        ],
        muscle: [
            { name: 'Incline Bench Press', sets: 4, reps: '8-10' },
            { name: 'Weighted Pull-ups', sets: 4, reps: '8-10' },
            { name: 'Front Squats', sets: 4, reps: '10-12' },
            { name: 'Lateral Raises', sets: 4, reps: '12-15' },
            { name: 'Cable Flyes', sets: 3, reps: '12-15' },
            { name: 'Leg Curls', sets: 3, reps: '12-15' }
        ],
        endurance: [
            { name: 'Burpee Box Jumps', sets: 4, reps: '12' },
            { name: 'Kettlebell Swings', sets: 4, reps: '20' },
            { name: 'Battle Ropes', sets: 4, reps: '45 seconds' },
            { name: 'Box Jumps', sets: 4, reps: '15' },
            { name: 'Rowing Machine', sets: 4, reps: '500m' },
            { name: 'Sprint Intervals', sets: 5, reps: '30 seconds' }
        ]
    },
    advanced: {
        strength: [
            { name: 'Heavy Back Squats', sets: 5, reps: '3-5' },
            { name: 'Heavy Bench Press', sets: 5, reps: '3-5' },
            { name: 'Heavy Deadlifts', sets: 5, reps: '3-5' },
            { name: 'Weighted Dips', sets: 4, reps: '6-8' },
            { name: 'Weighted Pull-ups', sets: 4, reps: '6-8' },
            { name: 'Front Squats', sets: 4, reps: '5-6' }
        ],
        muscle: [
            { name: 'Weighted Muscle-ups', sets: 4, reps: '6-8' },
            { name: 'Barbell Hip Thrusts', sets: 4, reps: '10-12' },
            { name: 'Deficit Deadlifts', sets: 4, reps: '8-10' },
            { name: 'Close-Grip Bench', sets: 4, reps: '8-10' },
            { name: 'Bulgarian Split Squats', sets: 4, reps: '10 each' },
            { name: 'Weighted Chin-ups', sets: 4, reps: '8-10' }
        ],
        endurance: [
            { name: 'Complex Burpees', sets: 5, reps: '15' },
            { name: 'Heavy Kettlebell Swings', sets: 5, reps: '30' },
            { name: 'Rowing Intervals', sets: 6, reps: '500m' },
            { name: 'Assault Bike Sprints', sets: 6, reps: '45 seconds' },
            { name: 'Tire Flips', sets: 5, reps: '10' },
            { name: 'Sled Pushes', sets: 5, reps: '40m' }
        ]
    }
};

// Day split templates
const daySplits = {
    3: ['Full Body A', 'Full Body B', 'Full Body C'],
    4: ['Upper Body', 'Lower Body', 'Upper Body', 'Lower Body'],
    5: ['Push', 'Pull', 'Legs', 'Upper Body', 'Full Body']
};

// Form submission handler
document.getElementById('workout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get user selections
    const level = document.getElementById('fitness-level').value;
    const goal = document.getElementById('goal').value;
    const days = parseInt(document.getElementById('days').value);
    
    // Validate inputs
    if (!level || !goal || !days) {
        alert('Please fill in all fields');
        return;
    }
    
    // Generate the workout plan
    generateWorkoutPlan(level, goal, days);
});

// Function to generate workout plan
function generateWorkoutPlan(level, goal, days) {
    const planContainer = document.getElementById('workout-plan');
    const workoutExercises = exercises[level][goal];
    const splits = daySplits[days];
    
    // Calculate exercises per day
    const exercisesPerDay = Math.ceil(workoutExercises.length / days);
    
    let html = '';
    
    // Create a card for each day
    for (let i = 0; i < days; i++) {
        const dayExercises = workoutExercises.slice(
            i * exercisesPerDay,
            (i + 1) * exercisesPerDay
        );
        
        if (dayExercises.length > 0) {
            html += `
                <div class="plan-card">
                    <h3>Day ${i + 1}: ${splits[i]}</h3>
                    ${dayExercises.map(exercise => `
                        <div class="exercise-item">
                            <div class="exercise-name">${exercise.name}</div>
                            <div class="exercise-details">${exercise.sets} sets × ${exercise.reps} reps</div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
    }
    
    // Display the workout plan
    planContainer.innerHTML = html;
    
    // Scroll to the plan
    planContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

