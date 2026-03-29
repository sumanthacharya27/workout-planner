let weeklyChart;
let distributionChart;

export function renderCharts(history = []) {
  const weekLabels = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  const weekly = [0,0,0,0,0,0,0];
  const distMap = {};

  history.forEach(item => {
    const date = new Date(item.workout_date);
    if (!Number.isNaN(date.getTime())) weekly[date.getDay()] += 1;
    distMap[item.workout_name] = (distMap[item.workout_name] || 0) + 1;
  });

  weeklyChart?.destroy();
  distributionChart?.destroy();

  weeklyChart = new Chart(document.getElementById('weeklyChart'), {
    type: 'bar',
    data: { labels: weekLabels, datasets: [{ label: 'Weekly Frequency', data: weekly, backgroundColor: '#FF6B35' }] }
  });

  distributionChart = new Chart(document.getElementById('distributionChart'), {
    type: 'doughnut',
    data: { labels: Object.keys(distMap), datasets: [{ label: 'Distribution', data: Object.values(distMap), backgroundColor: ['#FF6B35','#F7931E','#FFB347','#FFC371','#FFA07A','#FFD166'] }] }
  });
}
