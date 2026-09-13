(() => {
  // Theme management for all theme toggle buttons on the page
  const themeButtons = document.querySelectorAll('[data-theme-toggle]');

  const applyTheme = (theme) => {
    const isDark = theme === 'dark';
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;

    themeButtons.forEach((btn) => {
      const icon = btn.querySelector('[data-theme-icon]');
      const label = btn.querySelector('[data-theme-label]');
      const svg = btn.querySelector('svg');

      if (icon) {
        icon.textContent = isDark ? '🌙' : '☀️';
      }
      if (label) {
        label.textContent = isDark ? 'Dark' : 'Light';
      }
      if (svg && !icon) {
        // Switch between sun and moon icon for SVG toggle buttons
        if (isDark) {
          svg.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
        } else {
          svg.innerHTML = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        }
      }

      btn.setAttribute('aria-label', isDark ? 'Current theme: Dark. Click for Light.' : 'Current theme: Light. Click for Dark.');
      btn.setAttribute('aria-pressed', String(isDark));
    });
  };

  // Initial theme check: default to light
  const savedTheme = localStorage.getItem('hotelreserve-theme');
  const currentTheme = document.documentElement.dataset.theme || savedTheme || 'light';
  applyTheme(currentTheme);

  themeButtons.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const current = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
      const nextTheme = current === 'dark' ? 'light' : 'dark';
      localStorage.setItem('hotelreserve-theme', nextTheme);
      applyTheme(nextTheme);

      const preferenceUrl = btn.dataset.themePreferenceUrl;
      const csrf = btn.dataset.csrf;
      if (preferenceUrl && csrf) {
        fetch(preferenceUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ csrf, theme: nextTheme })
        }).catch(() => {});
      }
    });
  });

  // Mobile navigation toggle
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(open));
    });

    document.addEventListener('click', (e) => {
      if (nav.classList.contains('open') && !nav.contains(e.target) && e.target !== toggle) {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // Filter drawer toggle on mobile
  const filterBtn = document.querySelector('[data-filter-toggle]');
  const filterPanel = document.querySelector('[data-filter-panel]');
  if (filterBtn && filterPanel) {
    filterBtn.addEventListener('click', () => {
      const open = filterPanel.classList.toggle('open');
      filterBtn.setAttribute('aria-expanded', String(open));
      filterBtn.textContent = open ? 'Hide Filters' : 'Show Filters';
    });
  }

  // Budget price slider live output
  const range = document.querySelector('[data-price-range]');
  const output = document.querySelector('[data-price-output]');
  if (range && output) {
    range.addEventListener('input', () => {
      output.textContent = `₱${Number(range.value).toLocaleString()}`;
    });
  }

  // Prevent double form submissions globally
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (form.classList.contains('is-submitting')) {
        e.preventDefault();
        return;
      }
      form.classList.add('is-submitting');
      const btn = form.querySelector('button[type="submit"]');
      if (btn) {
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<span style="opacity: 0.7;">Processing...</span>';
        btn.style.pointerEvents = 'none';
      }
    });
  });

  // Favorites handling
  const favoritesUrl = document.body.dataset.favoritesUrl;
  const favoriteCsrf = document.body.dataset.csrf;
  const storedFavorites = new Set(JSON.parse(localStorage.getItem('hotelreserve-favorites') || '[]'));

  const renderFavorite = (button, favorites) => {
    const saved = favorites.has(String(button.dataset.favorite));
    button.classList.toggle('is-favorite', saved);
    button.textContent = saved ? '♥' : '♡';
    button.setAttribute('aria-pressed', String(saved));
    button.setAttribute('title', saved ? 'Remove from favorites' : 'Save to favorites');
  };

  const favoriteButtons = Array.from(document.querySelectorAll('[data-favorite]'));

  const bindFavorites = (favorites, persistFn) => {
    favoriteButtons.forEach((button) => {
      renderFavorite(button, favorites);
      button.addEventListener('click', (e) => {
        e.preventDefault();
        persistFn(button, favorites);
      });
    });
  };

  if (favoritesUrl && favoriteCsrf) {
    fetch(favoritesUrl)
      .then((res) => (res.ok ? res.json() : Promise.reject()))
      .then((data) => {
        const favorites = new Set((data.favorites || []).map(String));
        bindFavorites(favorites, (button) => {
          const roomId = String(button.dataset.favorite);
          fetch(favoritesUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ csrf: favoriteCsrf, room_id: roomId })
          })
            .then((res) => res.json())
            .then((resData) => {
              if (resData.saved) {
                favorites.add(roomId);
              } else {
                favorites.delete(roomId);
              }
              renderFavorite(button, favorites);
            })
            .catch(() => {});
        });
      })
      .catch(() => {
        bindLocalFavorites();
      });
  } else {
    bindLocalFavorites();
  }

  function bindLocalFavorites() {
    bindFavorites(storedFavorites, (button) => {
      const roomId = String(button.dataset.favorite);
      if (storedFavorites.has(roomId)) {
        storedFavorites.delete(roomId);
      } else {
        storedFavorites.add(roomId);
      }
      localStorage.setItem('hotelreserve-favorites', JSON.stringify([...storedFavorites]));
      renderFavorite(button, storedFavorites);
    });
  }
})();

// ── Toast Notification System ────────────────────────────────────────────────
(function () {
  const ICONS = {
    success: '✓',
    error:   '✕',
    warning: '⚠',
    info:    'ℹ',
  };

  const DURATION = {
    success: 4000,
    error:   6000,
    warning: 5000,
    info:    4500,
  };

  function showToast(type, message) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const normalType = ['success','error','warning','info'].includes(type) ? type : 'info';
    const duration   = DURATION[normalType];

    const toast = document.createElement('div');
    toast.className = 'toast toast-' + normalType;
    toast.style.setProperty('--toast-duration', duration + 'ms');
    toast.setAttribute('role', 'alert');
    toast.innerHTML =
      '<span class="toast-icon">' + (ICONS[normalType] || 'ℹ') + '</span>' +
      '<span class="toast-body">' + message + '</span>' +
      '<button class="toast-close" aria-label="Dismiss">&times;</button>' +
      '<div class="toast-progress"></div>';

    container.appendChild(toast);

    // Trigger slide-in on next frame
    requestAnimationFrame(() => {
      requestAnimationFrame(() => toast.classList.add('toast-show'));
    });

    // Auto-dismiss
    let timer = setTimeout(() => dismiss(toast), duration);

    // Pause on hover
    toast.addEventListener('mouseenter', () => clearTimeout(timer));
    toast.addEventListener('mouseleave', () => {
      timer = setTimeout(() => dismiss(toast), 1500);
    });

    // Manual close
    toast.querySelector('.toast-close').addEventListener('click', () => {
      clearTimeout(timer);
      dismiss(toast);
    });
  }

  function dismiss(toast) {
    toast.classList.add('toast-hide');
    toast.classList.remove('toast-show');
    toast.addEventListener('transitionend', () => toast.remove(), { once: true });
  }

  // Expose globally so any PHP page can call: window.toast('success', 'Done!')
  window.toast = showToast;

  // Fire PHP flash message automatically
  if (window.__flashToast) {
    const { type, msg } = window.__flashToast;
    // Small delay so the page is visually settled first
    setTimeout(() => showToast(type, msg), 350);
    delete window.__flashToast;
  }
})();