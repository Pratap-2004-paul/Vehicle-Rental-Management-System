// ===== VRMS Toast Notifications =====
function ensureToastContainer() {
  let el = document.getElementById('toastContainer');
  if (!el) {
    el = document.createElement('div');
    el.id = 'toastContainer';
    el.style.cssText = 'position:fixed; top:20px; right:20px; z-index:999; display:flex; flex-direction:column; gap:10px;';
    document.body.appendChild(el);
  }
  return el;
}

function showToast(message, type = 'success') {
  const container = ensureToastContainer();
  const colors = {
    success: { bg: '#dcfce7', text: '#16a34a', border: '#16a34a' },
    error: { bg: '#fee2e2', text: '#dc2626', border: '#dc2626' },
    info: { bg: '#e0f2fe', text: '#0369a1', border: '#0369a1' },
  };
  const c = colors[type] || colors.info;

  const toast = document.createElement('div');
  toast.textContent = message;
  toast.style.cssText = `
    background:${c.bg}; color:${c.text}; border-left:4px solid ${c.border};
    padding:12px 18px; border-radius:6px; font-size:14px; font-weight:500;
    box-shadow:0 4px 12px rgba(0,0,0,0.12); min-width:240px; max-width:360px;
    opacity:0; transform:translateX(20px); transition:0.25s ease;
  `;
  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(0)';
  });

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    setTimeout(() => toast.remove(), 250);
  }, 3500);
}