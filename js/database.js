import { API_BASE } from './config.js';

async function request(path, options = {}) {
  const response = await fetch(`${API_BASE}/${path}`, {
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    ...options
  });
  const payload = await response.json();
  if (!response.ok || !payload.success) throw new Error(payload.message || 'Request failed');
  return payload.data;
}

export const authApi = {
  register: body => request('auth.php?action=register', { method: 'POST', body: JSON.stringify(body) }),
  login: body => request('auth.php?action=login', { method: 'POST', body: JSON.stringify(body) }),
  logout: () => request('auth.php?action=logout', { method: 'POST', body: '{}' }),
  check: () => request('auth.php?action=check')
};

export const appApi = {
  getStats: () => request('simple.php?action=getStats'),
  saveWorkout: body => request('simple.php?action=saveWorkout', { method: 'POST', body: JSON.stringify(body) }),
  getWorkouts: () => request('simple.php?action=getWorkouts'),
  deleteWorkout: body => request('simple.php?action=deleteWorkout', { method: 'POST', body: JSON.stringify(body) }),
  saveLog: body => request('simple.php?action=saveLog', { method: 'POST', body: JSON.stringify(body) }),
  getHistory: (filter='all') => request(`simple.php?action=getHistory&filter=${encodeURIComponent(filter)}`)
};
