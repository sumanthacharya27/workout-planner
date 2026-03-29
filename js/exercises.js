export function setupExerciseBuilder(state) {
  console.log('=== EXERCISE BUILDER SETUP START ===');
  console.log('State object:', state);

  // Get all elements
  const openBtn = document.getElementById('openExerciseModal');
  const addBtn = document.getElementById('addExerciseBtn');
  const cancelBtn = document.getElementById('cancelExercise');
  const modal = document.getElementById('exerciseModal');
  const list = document.getElementById('exerciseList');

  console.log('Elements found:');
  console.log('- openBtn:', !!openBtn);
  console.log('- addBtn:', !!addBtn);
  console.log('- cancelBtn:', !!cancelBtn);
  console.log('- modal:', !!modal);
  console.log('- list:', !!list);

  if (!openBtn || !addBtn || !modal || !list) {
    console.error('ERROR: Missing required elements!');
    return;
  }

  // Open modal button
  openBtn.onclick = function(e) {
    console.log('OPEN CLICKED');
    e.preventDefault();
    document.getElementById('exName').value = '';
    document.getElementById('exSets').value = '3';
    document.getElementById('exReps').value = '10';
    document.getElementById('exWeight').value = '0';
    document.getElementById('exRest').value = '60';
    modal.classList.remove('hidden');
  };

  // Cancel button
  if (cancelBtn) {
    cancelBtn.onclick = function(e) {
      console.log('CANCEL CLICKED');
      e.preventDefault();
      modal.classList.add('hidden');
    };
  }

  // Add exercise button
  addBtn.onclick = function(e) {
    console.log('ADD CLICKED');
    e.preventDefault();

    const nameVal = document.getElementById('exName').value.trim();
    const setsVal = parseInt(document.getElementById('exSets').value);
    const repsVal = parseInt(document.getElementById('exReps').value);
    const weightVal = parseFloat(document.getElementById('exWeight').value);
    const restVal = parseInt(document.getElementById('exRest').value);

    console.log('Form values:', { nameVal, setsVal, repsVal, weightVal, restVal });

    if (!nameVal) {
      console.warn('Name is empty, not adding');
      alert('Please enter exercise name');
      return;
    }

    if (isNaN(setsVal) || setsVal < 1 || isNaN(repsVal) || repsVal < 1) {
      alert('Sets and reps must be at least 1');
      return;
    }

    const ex = {
      name: nameVal,
      sets: setsVal,
      reps: repsVal,
      weight: weightVal,
      rest: restVal,
    };

    console.log('Adding:', ex);
    state.builderExercises.push(ex);
    console.log('Total exercises now:', state.builderExercises.length);
    console.log('Full state.builderExercises:', state.builderExercises);

    updateList();
    modal.classList.add('hidden');
  };

  function updateList() {
    console.log('Updating list with', state.builderExercises.length, 'exercises');

    if (state.builderExercises.length === 0) {
      console.log('List is empty');
      list.innerHTML = '<p>No exercises added yet.</p>';
    } else {
      const html = state.builderExercises.map((ex, i) => {
        const card = `<div class="card" style="margin-bottom: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center; min-height: 40px;">
            <span><strong>${i + 1}. ${ex.name}</strong> - ${ex.sets}x${ex.reps} @ ${ex.weight}kg | ${ex.rest}s rest</span>
            <button type="button" onclick="window.__removeExercise(${i})" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; white-space: nowrap;">Remove</button>
          </div>
        </div>`;
        return card;
      }).join('');
      
      console.log('Generated HTML:', html);
      list.innerHTML = html;
      console.log('List innerHTML updated. Current list.innerHTML length:', list.innerHTML.length);
    }
  }

  // Store remove function globally for inline onclick
  window.__removeExercise = function(index) {
    console.log('Removing exercise at index:', index);
    state.builderExercises.splice(index, 1);
    updateList();
  };

  updateList();
  console.log('=== EXERCISE BUILDER SETUP END ===');
}
