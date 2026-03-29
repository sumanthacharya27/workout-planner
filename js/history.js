export function renderHistory(items) {
  const container = document.getElementById('historyList');
  container.innerHTML = items.map(log => `<article class="card"><strong>${log.workout_name}</strong><p>${log.workout_date} • ${log.duration_minutes} min</p></article>`).join('') || '<p>No history yet.</p>';
}
