// ===== VRMS Auth Helper =====

function isLoggedIn() {
  return !!getToken();
}

// Call this at the top of pages that REQUIRE login (dashboard, booking, my-bookings, etc.)
function requireAuth() {
  if (!isLoggedIn()) {
    window.location.href = 'login.html';
  }
}

// Call this at the top of admin-only pages
async function requireAdmin() {
  requireAuth();
  try {
    const user = await api.get('/me');
    setCurrentUser(user);
    if (user.role !== 'admin') {
      alert('Admins only.');
      window.location.href = 'dashboard.html';
    }
  } catch (e) {
    clearToken();
    window.location.href = 'login.html';
  }
}

async function logout() {
  try {
    await api.post('/logout');
  } catch (e) {
    // ignore errors, clear locally anyway
  }
  clearToken();
  window.location.href = 'index.html';
}

// Updates the navbar login/register buttons vs. user menu, on every page
function renderNavbarAuthState() {
  const guestActions = document.getElementById('navGuestActions');
  const userActions = document.getElementById('navUserActions');
  const userNameEl = document.getElementById('navUserName');

  if (!guestActions || !userActions) return;

  if (isLoggedIn()) {
    guestActions.classList.add('hidden');
    userActions.classList.remove('hidden');
    const user = getCurrentUser();
    if (userNameEl && user) {
      const isAdmin = user.role === 'admin';
      userNameEl.textContent = isAdmin ? 'Admin' : user.name;
      userNameEl.href = isAdmin ? 'admin-dashboard.html' : 'dashboard.html';
    }
  } else {
    guestActions.classList.remove('hidden');
    userActions.classList.add('hidden');
  }
  
}

document.addEventListener('DOMContentLoaded', renderNavbarAuthState);
