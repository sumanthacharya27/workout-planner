export function setupNavigation(onChange) {
  const navButtons = [...document.querySelectorAll('.nav-btn')];
  navButtons.forEach(button => {
    button.addEventListener('click', () => {
      navButtons.forEach(item => item.classList.remove('active'));
      button.classList.add('active');
      onChange(button.dataset.view);
    });
  });
}

export function showView(viewId) {
  document.querySelectorAll('.view').forEach(view => view.classList.add('hidden'));
  document.getElementById(viewId)?.classList.remove('hidden');
}
