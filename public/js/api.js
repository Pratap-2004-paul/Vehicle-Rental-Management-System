// ===== VRMS API Helper =====
// Because frontend and backend are on the SAME server/origin,
// we can use relative paths like '/api/...' — no need for a full domain.

const API_BASE = '/api';

function getToken() {
  return localStorage.getItem('vrms_token');
}

function setToken(token) {
  localStorage.setItem('vrms_token', token);
}

function clearToken() {
  localStorage.removeItem('vrms_token');
  localStorage.removeItem('vrms_user');
}

function getCurrentUser() {
  const raw = localStorage.getItem('vrms_user');
  return raw ? JSON.parse(raw) : null;
}

function setCurrentUser(user) {
  localStorage.setItem('vrms_user', JSON.stringify(user));
}

/**
 * Core request helper.
 * @param {string} endpoint  e.g. '/vehicles' or '/bookings/5'
 * @param {object} options   { method, body }
 */
async function apiRequest(endpoint, options = {}) {
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  const token = getToken();
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API_BASE + endpoint, {
    method: options.method || 'GET',
    headers,
    body: options.body ? JSON.stringify(options.body) : undefined,
  });

  const data = await response.json().catch(() => ({}));

  if (!response.ok) {
    // Attach server error details so calling code can show them
    const error = new Error(data.message || 'Something went wrong');
    error.status = response.status;
    error.errors = data.errors || null;
    throw error;
  }

  return data;
}

// Convenience wrappers
const api = {
  get: (endpoint) => apiRequest(endpoint, { method: 'GET' }),
  post: (endpoint, body) => apiRequest(endpoint, { method: 'POST', body }),
  put: (endpoint, body) => apiRequest(endpoint, { method: 'PUT', body }),
  delete: (endpoint) => apiRequest(endpoint, { method: 'DELETE' }),
};
