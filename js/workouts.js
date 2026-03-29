import { PREMADE_WORKOUTS } from './config.js';

export function renderPremadeWorkouts(filter = 'all', onStart) {
  const grid = document.getElementById('premadeGrid');
  const filtered = PREMADE_WORKOUTS.filter(workout => filter === 'all' || workout.difficulty === filter);
  grid.innerHTML = filtered.map(workout => `
    <article class="card">
      <h3>${workout.name}</h3>
      <p>${workout.description}</p>
      <small>${workout.difficulty}</small>
      <button class="btn start-premade" data-id="${workout.id}">Start</button>
    </article>
  `).join('');

  grid.querySelectorAll('.start-premade').forEach(button => {
    button.addEventListener('click', () => {
      const selected = PREMADE_WORKOUTS.find(item => item.id === button.dataset.id);
      onStart(selected);
    });
  });
}

export function renderCustomWorkouts(workouts, onStart, onDelete) {
  const grid = document.getElementById('customGrid');
  grid.innerHTML = workouts.map(workout => `
    <article class="card">
      <h3>${workout.name}</h3>
      <p>${workout.description || ''}</p>
      <small>${workout.difficulty}</small>
      <div class="row">
        <button class="btn start-custom" data-id="${workout.id}">Start</button>
        <button class="btn btn-outline del-custom" data-id="${workout.id}">Delete</button>
      </div>
    </article>
  `).join('') || '<p>No custom workouts yet.</p>';

  grid.querySelectorAll('.start-custom').forEach(btn => btn.addEventListener('click', () => onStart(workouts.find(w => String(w.id) === btn.dataset.id))));
  grid.querySelectorAll('.del-custom').forEach(btn => btn.addEventListener('click', () => onDelete(Number(btn.dataset.id))));
}
