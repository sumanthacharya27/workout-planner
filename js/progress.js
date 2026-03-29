export function renderAchievements(stats = {}) {
  const streak = Number(stats.streak_days || 0);
  const workouts = Number(stats.total_workouts || 0);
  const achievements = [
    ['🎉 First Workout', workouts >= 1],
    ['⚡ Week Warrior', streak >= 7],
    ['💯 Century Club', workouts >= 100],
    ['👑 Consistency King', streak >= 30],
  ];

  document.getElementById('achievements').innerHTML = achievements
    .map(([name, unlocked]) => `<article class="card achievement ${unlocked ? '' : 'locked'}"><h4>${name}</h4><p>${unlocked ? 'Unlocked' : 'Locked'}</p></article>`)
    .join('');
}
