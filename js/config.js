export const API_BASE = 'php';

export const PREMADE_WORKOUTS = [
  { id: 'p1', name: 'Beginner Full Body', difficulty: 'Beginner', description: 'Balanced fundamentals for new lifters.', exercises: [
    { name: 'Goblet Squat', sets: 3, reps: 12, weight: 20, rest: 60 },
    { name: 'Push Up', sets: 3, reps: 10, weight: 0, rest: 45 },
    { name: 'Row', sets: 3, reps: 12, weight: 25, rest: 60 }
  ] },
  { id: 'p2', name: 'Upper Strength', difficulty: 'Intermediate', description: 'Press and pull strength focus.', exercises: [
    { name: 'Bench Press', sets: 5, reps: 5, weight: 135, rest: 120 },
    { name: 'Barbell Row', sets: 4, reps: 8, weight: 95, rest: 90 },
    { name: 'Overhead Press', sets: 4, reps: 6, weight: 75, rest: 90 }
  ] },
  { id: 'p3', name: 'Leg Power', difficulty: 'Intermediate', description: 'Heavy lower-body performance day.', exercises: [
    { name: 'Back Squat', sets: 5, reps: 5, weight: 185, rest: 120 },
    { name: 'Romanian Deadlift', sets: 4, reps: 8, weight: 155, rest: 90 },
    { name: 'Walking Lunge', sets: 3, reps: 12, weight: 40, rest: 60 }
  ] },
  { id: 'p4', name: 'HIIT Blast', difficulty: 'Advanced', description: 'Conditioning and calorie burn.', exercises: [
    { name: 'Burpees', sets: 5, reps: 15, weight: 0, rest: 30 },
    { name: 'Kettlebell Swing', sets: 4, reps: 20, weight: 35, rest: 30 },
    { name: 'Mountain Climbers', sets: 4, reps: 30, weight: 0, rest: 30 }
  ] },
  { id: 'p5', name: 'Core Crusher', difficulty: 'Beginner', description: 'Core stability and endurance.', exercises: [
    { name: 'Plank', sets: 3, reps: 60, weight: 0, rest: 45 },
    { name: 'Dead Bug', sets: 3, reps: 12, weight: 0, rest: 45 },
    { name: 'Russian Twist', sets: 3, reps: 20, weight: 10, rest: 45 }
  ] },
  { id: 'p6', name: 'Powerlifting Day', difficulty: 'Advanced', description: 'Big three strength progression.', exercises: [
    { name: 'Squat', sets: 5, reps: 3, weight: 225, rest: 180 },
    { name: 'Bench Press', sets: 5, reps: 3, weight: 185, rest: 180 },
    { name: 'Deadlift', sets: 3, reps: 3, weight: 275, rest: 180 }
  ] }
];
