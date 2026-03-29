export function renderDashboard(data) {
  const statsGrid = document.getElementById('statsGrid');
  const recentList = document.getElementById('recentList');
  const stats = data?.stats || {};

  const cards = [
    ['🏋️ Workouts', stats.total_workouts || 0],
    ['🔥 Streak', `${stats.streak_days || 0} days`],
    ['⏱️ Hours', ((stats.total_time_minutes || 0) / 60).toFixed(1)],
    ['✅ Exercises', stats.total_exercises || 0],
  ];

  statsGrid.innerHTML = cards.map(([label, value]) => `<article class="card"><h4>${label}</h4><p>${value}</p></article>`).join('');
  recentList.innerHTML = (data?.recent || []).map(item => `<li class="card">${item.workout_name} - ${item.duration_minutes} min (${item.workout_date})</li>`).join('') || '<li class="card">No workouts yet.</li>';
}
