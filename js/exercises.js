export function setupExerciseBuilder(state) {
  const modal = document.getElementById('exerciseModal');
  const openBtn = document.getElementById('openExerciseModal');
  const cancelBtn = document.getElementById('cancelExercise');
  const addBtn = document.getElementById('addExerciseBtn');

  const list = document.getElementById('exerciseList');

  const render = () => {
    list.innerHTML = state.builderExercises.map((item, index) => `<div class="card">${index + 1}. ${item.name} - ${item.sets}x${item.reps} @ ${item.weight}kg | ${item.rest}s rest</div>`).join('') || '<p>No exercises added yet.</p>';
  };

  openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
  cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

  addBtn.addEventListener('click', () => {
    const exercise = {
      name: document.getElementById('exName').value.trim(),
      sets: Number(document.getElementById('exSets').value || 0),
      reps: Number(document.getElementById('exReps').value || 0),
      weight: Number(document.getElementById('exWeight').value || 0),
      rest: Number(document.getElementById('exRest').value || 0),
    };
    if (!exercise.name) return;
    state.builderExercises.push(exercise);
    modal.classList.add('hidden');
    render();
  });

  render();
  return { render };
}
