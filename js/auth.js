import { authApi } from './database.js';

const isLoginPage = window.location.pathname.endsWith('login.html');
if (!isLoginPage) {
  authApi.check().catch(() => window.location.href = 'login.html');
}

if (isLoginPage) {
  const loginTab = document.getElementById('loginTab');
  const registerTab = document.getElementById('registerTab');
  const registerExtras = document.getElementById('registerExtras');
  const form = document.getElementById('authForm');
  const submit = document.getElementById('authSubmit');
  const message = document.getElementById('authMessage');
  let mode = 'login';

  function setMode(nextMode) {
    mode = nextMode;
    loginTab.classList.toggle('active', mode === 'login');
    registerTab.classList.toggle('active', mode === 'register');
    registerExtras.classList.toggle('hidden', mode !== 'register');
    submit.textContent = mode === 'login' ? 'Login' : 'Register';
  }

  loginTab?.addEventListener('click', () => setMode('login'));
  registerTab?.addEventListener('click', () => setMode('register'));

  form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(form);
    const body = Object.fromEntries(formData.entries());
    if (mode === 'login') delete body.name;

    try {
      if (mode === 'login') await authApi.login(body);
      else await authApi.register(body);
      window.location.href = 'index.html';
    } catch (error) {
      message.textContent = error.message;
    }
  });
}
