const PREMADE = [
  {
    id: 'beginner-full-body',
    name: 'Beginner Full Body',
    difficulty: 'beginner',
    description: 'Full-body fundamentals with minimal equipment.',
    exercises: [
      { name: 'Bodyweight Squat', sets: 3, reps: 12, rest: 60 },
      { name: 'Push-up', sets: 3, reps: 10, rest: 60 },
      { name: 'Plank', sets: 3, reps: 30, rest: 45 }
    ]
  },
  {
    id: 'upper-strength',
    name: 'Upper Body Strength',
    difficulty: 'intermediate',
    description: 'Compound and accessory lifts for upper strength.',
    exercises: [
      { name: 'Bench Press', sets: 4, reps: 6, rest: 90 },
      { name: 'Barbell Row', sets: 4, reps: 8, rest: 90 },
      { name: 'Overhead Press', sets: 3, reps: 8, rest: 75 }
    ]
  },
  {
    id: 'leg-power',
    name: 'Leg Day Power',
    difficulty: 'advanced',
    description: 'High-intensity lower body session.',
    exercises: [
      { name: 'Back Squat', sets: 5, reps: 5, rest: 120 },
      { name: 'Romanian Deadlift', sets: 4, reps: 8, rest: 90 },
      { name: 'Walking Lunge', sets: 3, reps: 12, rest: 75 }
    ]
  }
];

const STORAGE = {
  custom: 'gymplanner_custom_v2',
  history: 'gymplanner_history_v2'
};

const state = {
  filter: 'all',
  customDraftExercises: [],
  currentWorkout: null,
  exerciseIndex: 0,
  startedAt: null
};

const getData = (key, fallback = []) => JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
const setData = (key, value) => localStorage.setItem(key, JSON.stringify(value));

const allWorkouts = () => [...PREMADE, ...getData(STORAGE.custom)];

function navigate(sectionId) {
  document.querySelectorAll('.app-section').forEach((el) => el.classList.add('hidden'));
  document.getElementById(sectionId)?.classList.remove('hidden');

  document.querySelectorAll('.nav-btn').forEach((btn) => {
    const active = btn.dataset.section === sectionId;
    btn.classList.toggle('bg-brand-600', active);
    btn.classList.toggle('text-white', active);
    btn.classList.toggle('hover:bg-slate-100', !active);
  });

  if (sectionId === 'dashboard') renderDashboard();
  if (sectionId === 'workouts') renderWorkoutLibrary();
  if (sectionId === 'custom') renderCustomSection();
  if (sectionId === 'history') renderHistory();
  if (sectionId === 'progress') renderProgress();
}

function renderDashboard() {
  const history = getData(STORAGE.history);
  const totalWorkouts = history.length;
  const totalExercises = history.reduce((sum, w) => sum + w.exercises.length, 0);
  const totalMinutes = history.reduce((sum, w) => sum + w.duration, 0);
  const currentStreak = calcStreak(history);

  const cards = [
    ['Total Workouts', totalWorkouts, '🔥'],
    ['Current Streak', `${currentStreak} days`, '📅'],
    ['Training Time', `${totalMinutes} min`, '⏱️'],
    ['Exercises Done', totalExercises, '💪']
  ];

  document.getElementById('statsGrid').innerHTML = cards.map(([label, value, icon]) => `
    <article class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-2xl">${icon}</p>
      <p class="text-2xl font-bold mt-2">${value}</p>
      <p class="text-sm text-slate-500">${label}</p>
    </article>`).join('');

  const recent = history.slice(-5).reverse();
  document.getElementById('recentWorkouts').innerHTML = recent.length
    ? recent.map((h) => `<div class="rounded-lg border border-slate-200 p-3"><p class="font-semibold">${h.name}</p><p class="text-slate-500">${new Date(h.completedAt).toLocaleString()} · ${h.duration} min</p></div>`).join('')
    : '<p class="text-slate-500">No workouts completed yet.</p>';
}

function renderWorkoutLibrary() {
  const filters = ['all', 'beginner', 'intermediate', 'advanced'];
  document.getElementById('difficultyFilters').innerHTML = filters.map((f) => `
    <button class="filter px-3 py-1.5 rounded-full border ${state.filter === f ? 'bg-brand-600 text-white border-brand-600' : 'border-slate-300'}" data-filter="${f}">${capitalize(f)}</button>
  `).join('');

  document.querySelectorAll('.filter').forEach((btn) => {
    btn.addEventListener('click', () => {
      state.filter = btn.dataset.filter;
      renderWorkoutLibrary();
    });
  });

  const workouts = allWorkouts().filter((w) => state.filter === 'all' || w.difficulty === state.filter);
  document.getElementById('workoutCards').innerHTML = workouts.map((w) => `
    <article class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3">
      <div class="flex justify-between items-start gap-3">
        <h3 class="font-semibold text-lg">${w.name}</h3>
        <span class="text-xs uppercase rounded-full bg-slate-100 px-2 py-1">${w.difficulty}</span>
      </div>
      <p class="text-sm text-slate-600">${w.description || 'No description provided.'}</p>
      <p class="text-sm text-slate-500">${w.exercises.length} exercises · ~${estimate(w.exercises)} min</p>
      <button class="start-workout rounded-lg bg-brand-600 text-white px-4 py-2 mt-auto" data-id="${w.id}">Start Workout</button>
    </article>
  `).join('') || '<p class="text-slate-500">No workouts match this filter.</p>';

  document.querySelectorAll('.start-workout').forEach((btn) => {
    btn.addEventListener('click', () => startWorkout(btn.dataset.id));
  });
}

function renderCustomSection() {
  const custom = getData(STORAGE.custom);
  document.getElementById('exercisePreview').innerHTML = state.customDraftExercises.length
    ? state.customDraftExercises.map((ex, idx) => `<div class="rounded-lg border border-slate-200 p-3 text-sm flex justify-between"><span>${ex.name} · ${ex.sets}x${ex.reps} · ${ex.rest}s rest</span><button class="text-rose-600" onclick="removeDraftExercise(${idx})">Remove</button></div>`).join('')
    : '<p class="text-sm text-slate-500">No exercises added yet.</p>';

  document.getElementById('customWorkouts').innerHTML = custom.length
    ? custom.map((w) => `<article class="rounded-lg border border-slate-200 p-4"><h4 class="font-semibold">${w.name}</h4><p class="text-sm text-slate-500 mb-3">${w.exercises.length} exercises</p><button class="rounded-md bg-brand-600 text-white px-3 py-1.5 text-sm" onclick="startWorkout('${w.id}')">Start</button></article>`).join('')
    : '<p class="text-slate-500">No saved custom workouts yet.</p>';
}

window.removeDraftExercise = (index) => {
  state.customDraftExercises.splice(index, 1);
  renderCustomSection();
};

function addDraftExercise() {
  const ex = {
    name: document.getElementById('exerciseName').value.trim(),
    sets: Number(document.getElementById('exerciseSets').value),
    reps: Number(document.getElementById('exerciseReps').value),
    rest: Number(document.getElementById('exerciseRest').value)
  };
  if (!ex.name || ex.sets < 1 || ex.reps < 1 || ex.rest < 0) return alert('Enter valid exercise details.');

  state.customDraftExercises.push(ex);
  document.getElementById('exerciseName').value = '';
  renderCustomSection();
}

function saveCustomWorkout() {
  const name = document.getElementById('workoutName').value.trim();
  const description = document.getElementById('workoutDescription').value.trim();
  if (!name) return alert('Workout name is required.');
  if (!state.customDraftExercises.length) return alert('Add at least one exercise.');

  const custom = getData(STORAGE.custom);
  custom.push({
    id: `custom-${Date.now()}`,
    name,
    difficulty: 'intermediate',
    description,
    exercises: [...state.customDraftExercises]
  });
  setData(STORAGE.custom, custom);
  clearCustomForm();
  renderCustomSection();
  renderWorkoutLibrary();
  alert('Workout saved.');
}

function clearCustomForm() {
  state.customDraftExercises = [];
  ['workoutName', 'workoutDescription', 'exerciseName'].forEach((id) => (document.getElementById(id).value = ''));
  document.getElementById('exerciseSets').value = 3;
  document.getElementById('exerciseReps').value = 10;
  document.getElementById('exerciseRest').value = 60;
}

function startWorkout(id) {
  const workout = allWorkouts().find((w) => w.id === id);
  if (!workout) return;
  state.currentWorkout = workout;
  state.exerciseIndex = 0;
  state.startedAt = Date.now();
  navigate('execute');
  renderExecution();
}

function renderExecution() {
  const { currentWorkout, exerciseIndex } = state;
  if (!currentWorkout) return;

  const ex = currentWorkout.exercises[exerciseIndex];
  const total = currentWorkout.exercises.length;
  const pct = Math.round(((exerciseIndex + 1) / total) * 100);

  document.getElementById('executeTitle').textContent = currentWorkout.name;
  document.getElementById('exerciseCounter').textContent = `Exercise ${exerciseIndex + 1} of ${total}`;
  document.getElementById('progressBar').style.width = `${pct}%`;
  document.getElementById('exerciseNow').innerHTML = `
    <h3 class="font-semibold text-xl">${ex.name}</h3>
    <p class="text-slate-600 mt-1">${ex.sets} sets × ${ex.reps} reps · Rest ${ex.rest}s</p>`;

  document.getElementById('prevExercise').disabled = exerciseIndex === 0;
  document.getElementById('nextExercise').classList.toggle('hidden', exerciseIndex >= total - 1);
  document.getElementById('completeWorkout').classList.toggle('hidden', exerciseIndex < total - 1);
}

function completeWorkout() {
  if (!state.currentWorkout) return;
  const duration = Math.max(1, Math.round((Date.now() - state.startedAt) / 60000));
  const history = getData(STORAGE.history);
  history.push({
    id: `session-${Date.now()}`,
    name: state.currentWorkout.name,
    exercises: state.currentWorkout.exercises,
    completedAt: new Date().toISOString(),
    duration
  });
  setData(STORAGE.history, history);

  state.currentWorkout = null;
  state.exerciseIndex = 0;
  state.startedAt = null;

  navigate('dashboard');
}

function renderHistory() {
  const history = getData(STORAGE.history).slice().reverse();
  document.getElementById('historyList').innerHTML = history.length
    ? history.map((h) => `<article class="bg-white rounded-xl border border-slate-200 p-4"><div class="flex justify-between items-center"><h3 class="font-semibold">${h.name}</h3><span class="text-xs text-slate-500">${new Date(h.completedAt).toLocaleString()}</span></div><p class="text-sm text-slate-600 mt-2">${h.exercises.length} exercises · ${h.duration} min</p></article>`).join('')
    : '<p class="text-slate-500">No workout history yet.</p>';
}

function renderProgress() {
  const history = getData(STORAGE.history);
  const totalWorkouts = history.length;
  const totalMinutes = history.reduce((sum, h) => sum + h.duration, 0);
  const totalExercises = history.reduce((sum, h) => sum + h.exercises.length, 0);
  const milestones = [
    ['First Workout', totalWorkouts >= 1],
    ['Five Workouts', totalWorkouts >= 5],
    ['Ten Hours Trained', totalMinutes >= 600],
    ['100 Exercises Completed', totalExercises >= 100]
  ];

  document.getElementById('milestones').innerHTML = milestones.map(([name, ok]) => `
    <article class="rounded-xl border p-4 ${ok ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200'}">
      <p class="text-sm ${ok ? 'text-emerald-700' : 'text-slate-500'}">${ok ? 'Unlocked' : 'Locked'}</p>
      <h3 class="font-semibold">${name}</h3>
    </article>`).join('');

  const recordMap = {};
  history.forEach((session) => {
    session.exercises.forEach((ex) => {
      const volume = ex.sets * ex.reps;
      if (!recordMap[ex.name] || recordMap[ex.name] < volume) recordMap[ex.name] = volume;
    });
  });

  const entries = Object.entries(recordMap).sort((a, b) => b[1] - a[1]).slice(0, 8);
  document.getElementById('records').innerHTML = entries.length
    ? entries.map(([name, value]) => `<div class="rounded-lg border border-slate-200 p-3"><p class="font-medium">${name}</p><p class="text-slate-500">Best set volume: ${value}</p></div>`).join('')
    : '<p class="text-slate-500">Complete workouts to generate personal records.</p>';
}

function estimate(exercises) {
  return Math.round(exercises.reduce((sum, ex) => sum + (ex.sets * ex.reps * 3 + ex.rest * ex.sets), 0) / 60);
}

function calcStreak(history) {
  if (!history.length) return 0;
  const days = new Set(history.map((h) => new Date(h.completedAt).toISOString().slice(0, 10)));
  let streak = 0;
  let cursor = new Date();
  while (days.has(cursor.toISOString().slice(0, 10))) {
    streak += 1;
    cursor.setDate(cursor.getDate() - 1);
  }
  return streak;
}

function capitalize(value) {
  return value.charAt(0).toUpperCase() + value.slice(1);
}

function boot() {
  document.getElementById('menuToggle').addEventListener('click', () => {
    document.getElementById('topNav').classList.toggle('hidden');
  });

  document.querySelectorAll('.nav-btn').forEach((btn) => btn.addEventListener('click', () => navigate(btn.dataset.section)));
  document.querySelectorAll('.quick-btn').forEach((btn) => btn.addEventListener('click', () => navigate(btn.dataset.section)));

  document.getElementById('addExercise').addEventListener('click', addDraftExercise);
  document.getElementById('saveWorkout').addEventListener('click', saveCustomWorkout);
  document.getElementById('clearWorkout').addEventListener('click', () => {
    clearCustomForm();
    renderCustomSection();
  });

  document.getElementById('prevExercise').addEventListener('click', () => {
    state.exerciseIndex = Math.max(0, state.exerciseIndex - 1);
    renderExecution();
  });
  document.getElementById('nextExercise').addEventListener('click', () => {
    state.exerciseIndex = Math.min(state.currentWorkout.exercises.length - 1, state.exerciseIndex + 1);
    renderExecution();
  });
  document.getElementById('completeWorkout').addEventListener('click', completeWorkout);
  document.getElementById('exitWorkout').addEventListener('click', () => {
    if (confirm('Exit current workout?')) {
      state.currentWorkout = null;
      navigate('workouts');
    }
  });

  navigate('dashboard');
}

document.addEventListener('DOMContentLoaded', boot);
