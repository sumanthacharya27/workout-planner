import './auth.js';
import { authApi, appApi } from './database.js';
import { setupNavigation, showView } from './navigation.js';
import { renderDashboard } from './dashboard.js';
import { renderPremadeWorkouts, renderCustomWorkouts } from './workouts.js';
import { setupExerciseBuilder } from './exercises.js';
import { renderHistory } from './history.js';
import { renderAchievements } from './progress.js';
import { renderCharts } from './charts.js';

const state = {
  builderExercises: [],
  customWorkouts: [],
  history: [],
  activeWorkout: null,
  activeIndex: 0,
  startedAt: null,
  stats: {}
};

async function init() {
  const auth = await authApi.check();
  document.getElementById('welcomeName').textContent = `Hi, ${auth.name || 'Athlete'} 👋`;

  setupNavigation(async view => {
    showView(view);
    if (view === 'history') await loadHistory();
    if (view === 'progress') {
      await loadHistory();
      renderCharts(state.history);
      renderAchievements(state.stats);
    }
  });

  setupExerciseBuilder(state);
  bindBuilder();
  bindExecution();
  bindFilters();

  await refreshAll();
}

function bindFilters() {
  document.getElementById('difficultyFilter').addEventListener('change', (event) => {
    renderPremadeWorkouts(event.target.value, startWorkout);
  });

  document.getElementById('historyFilter').addEventListener('change', async (event) => {
    const data = await appApi.getHistory(event.target.value);
    state.history = data;
    renderHistory(data);
  });

  document.getElementById('logoutBtn').addEventListener('click', async () => {
    await authApi.logout();
    window.location.href = 'login.html';
  });
}

function bindBuilder() {
  const workoutNameInput = document.getElementById('workoutName');
  const exerciseSection = document.getElementById('exerciseSection');

  workoutNameInput.addEventListener('input', () => {
    const hasName = workoutNameInput.value.trim().length > 0;
    exerciseSection.classList.toggle('hidden', !hasName);
  });

  document.getElementById('saveWorkoutBtn').addEventListener('click', async () => {
    const payload = {
      name: document.getElementById('workoutName').value.trim(),
      difficulty: document.getElementById('workoutDifficulty').value,
      description: document.getElementById('workoutDescription').value.trim(),
      exercises: state.builderExercises
    };

    if (!payload.name || !payload.exercises.length) return alert('Please add workout name and exercises.');

    await appApi.saveWorkout(payload);
    state.builderExercises = [];
    document.getElementById('exerciseList').innerHTML = '<p>No exercises added yet.</p>';
    document.getElementById('workoutName').value = '';
    document.getElementById('workoutDescription').value = '';
    exerciseSection.classList.add('hidden');
    await loadCustomWorkouts();
    showView('workouts');
  });
}

function bindExecution() {
  document.getElementById('prevExercise').addEventListener('click', () => {
    state.activeIndex = Math.max(0, state.activeIndex - 1);
    renderExecution();
  });

  document.getElementById('nextExercise').addEventListener('click', () => {
    state.activeIndex = Math.min(state.activeWorkout.exercises.length - 1, state.activeIndex + 1);
    renderExecution();
  });

  document.getElementById('finishWorkout').addEventListener('click', async () => {
    const duration = Math.max(1, Math.round((Date.now() - state.startedAt) / 60000));
    await appApi.saveLog({
      workout_plan_id: Number(state.activeWorkout.id) || null,
      workout_name: state.activeWorkout.name,
      date: new Date().toISOString().slice(0, 10),
      duration_minutes: duration,
      exercises: state.activeWorkout.exercises
    });

    alert('Workout completed!');
    showView('dashboard');
    await refreshAll();
  });
}

function startWorkout(workout) {
  state.activeWorkout = workout;
  state.activeIndex = 0;
  state.startedAt = Date.now();
  showView('execution');
  renderExecution();
}

function renderExecution() {
  const list = state.activeWorkout.exercises;
  const current = list[state.activeIndex];
  document.getElementById('execTitle').textContent = state.activeWorkout.name;
  document.getElementById('execCounter').textContent = `Exercise ${state.activeIndex + 1} of ${list.length}`;
  const percent = Math.round(((state.activeIndex + 1) / list.length) * 100);
  document.getElementById('execProgress').style.width = `${percent}%`;
  document.getElementById('execExercise').innerHTML = `
    <h3>${current.name}</h3>
    <p>${current.sets} sets × ${current.reps} reps @ ${current.weight}kg</p>
    <p>Rest: ${current.rest || current.rest_seconds || 60}s</p>
    <div>${Array.from({ length: current.sets }).map((_,i) => `<label><input type="checkbox" /> Set ${i + 1}</label>`).join(' ')}</div>
  `;
  document.getElementById('finishWorkout').classList.toggle('hidden', state.activeIndex !== list.length - 1);
}

async function refreshAll() {
  const statsPayload = await appApi.getStats();
  state.stats = statsPayload.stats;
  renderDashboard(statsPayload);

  await loadCustomWorkouts();
  renderPremadeWorkouts('all', startWorkout);
  await loadHistory();
  renderAchievements(state.stats);
  renderCharts(state.history);
}

async function loadCustomWorkouts() {
  state.customWorkouts = await appApi.getWorkouts();
  renderCustomWorkouts(state.customWorkouts, startWorkout, async (planId) => {
    await appApi.deleteWorkout({ plan_id: planId });
    await loadCustomWorkouts();
  });
}

async function loadHistory() {
  const filter = document.getElementById('historyFilter')?.value || 'all';
  state.history = await appApi.getHistory(filter);
  renderHistory(state.history);
}

init().catch(() => {
  window.location.href = 'login.html';
});
