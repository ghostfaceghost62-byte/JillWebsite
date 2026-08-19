(() => {
  const themeButton = document.querySelector('[data-theme-toggle]');
  const preferenceUrl = themeButton?.dataset.themePreferenceUrl;
  const csrf = themeButton?.dataset.csrf;
  const applyTheme = (theme) => {
    const isDark = theme === 'dark';
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;
    if (themeButton) {
      themeButton.querySelector('[data-theme-icon]').textContent = isDark ? String.fromCodePoint(0x2600, 0xFE0F) : String.fromCodePoint(0x1F319);
      themeButton.querySelector('[data-theme-label]').textContent = isDark ? 'Light Mode' : 'Dark Mode';
      themeButton.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
      themeButton.setAttribute('aria-pressed', String(isDark));
    }
  };
  applyTheme(document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');
  themeButton?.addEventListener('click', () => {
    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('hotelreserve-theme', nextTheme);
    applyTheme(nextTheme);
    if (preferenceUrl && csrf) fetch(preferenceUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ csrf, theme: nextTheme }) }).catch(() => {});
  });
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#site-nav');
  if (toggle && nav) toggle.addEventListener('click', () => { const open = nav.classList.toggle('open'); toggle.setAttribute('aria-expanded', String(open)); });
  document.querySelector('[data-filter-toggle]')?.addEventListener('click', (event) => { const panel = document.querySelector('[data-filter-panel]'); const open = panel?.classList.toggle('open'); event.currentTarget.setAttribute('aria-expanded', String(open)); });
  const range = document.querySelector('[data-price-range]'); const output = document.querySelector('[data-price-output]');
  range?.addEventListener('input', () => { if (output) output.textContent = `\u20B1${Number(range.value).toLocaleString()}`; });
  const favoritesUrl = document.body.dataset.favoritesUrl; const favoriteCsrf = document.body.dataset.csrf;
  const storedFavorites = new Set(JSON.parse(localStorage.getItem('hotelreserve-favorites') || '[]'));
  const renderFavorite = (button, favorites) => { const saved = favorites.has(button.dataset.favorite); button.classList.toggle('is-favorite', saved); button.textContent = saved ? String.fromCodePoint(0x2665) : String.fromCodePoint(0x2661); button.setAttribute('aria-pressed', String(saved)); };
  const favoriteButtons = [...document.querySelectorAll('[data-favorite]')];
  const bindFavorites = (favorites, persist) => favoriteButtons.forEach((button) => { renderFavorite(button, favorites); button.addEventListener('click', () => { persist(button, favorites); }); });
  if (favoritesUrl && favoriteCsrf) {
    fetch(favoritesUrl).then((response) => response.json()).then((data) => {
      const favorites = new Set((data.favorites || []).map(String));
      bindFavorites(favorites, (button) => fetch(favoritesUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ csrf: favoriteCsrf, room_id: button.dataset.favorite }) }).then((response) => response.json()).then((data) => { data.saved ? favorites.add(String(data.room_id)) : favorites.delete(String(data.room_id)); renderFavorite(button, favorites); }).catch(() => {}));
    }).catch(() => bindFavorites(storedFavorites, (button) => { storedFavorites.has(button.dataset.favorite) ? storedFavorites.delete(button.dataset.favorite) : storedFavorites.add(button.dataset.favorite); localStorage.setItem('hotelreserve-favorites', JSON.stringify([...storedFavorites])); renderFavorite(button, storedFavorites); }));
  } else bindFavorites(storedFavorites, (button) => { storedFavorites.has(button.dataset.favorite) ? storedFavorites.delete(button.dataset.favorite) : storedFavorites.add(button.dataset.favorite); localStorage.setItem('hotelreserve-favorites', JSON.stringify([...storedFavorites])); renderFavorite(button, storedFavorites); });
})();