// CamHacker - Main JavaScript

(function() {
  'use strict';

  // Theme Management
  const getStoredTheme = () => localStorage.getItem('theme');
  const setStoredTheme = theme => localStorage.setItem('theme', theme);
  const getPreferredTheme = () => getStoredTheme() || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

  const setTheme = theme => {
    const resolved = theme === 'auto'
      ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
      : theme;
    document.documentElement.setAttribute('data-bs-theme', resolved);
  };

  const updateThemeUI = (theme) => {
    document.querySelectorAll('[data-bs-theme-value]').forEach(el => {
      el.classList.toggle('active', el.getAttribute('data-bs-theme-value') === theme);
    });
    const icon = document.querySelector('#theme-toggle i');
    if (icon) {
      const icons = { light: 'bi-sun-fill', dark: 'bi-moon-stars-fill', auto: 'bi-circle-half' };
      icon.className = 'bi ' + (icons[theme] || 'bi-circle-half');
    }
  };

  // Apply theme
  setTheme(getPreferredTheme());

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const stored = getStoredTheme();
    if (!stored || stored === 'auto') setTheme('auto');
  });

  document.addEventListener('DOMContentLoaded', () => {
    updateThemeUI(getStoredTheme() || 'auto');

    document.querySelectorAll('[data-bs-theme-value]').forEach(btn => {
      btn.addEventListener('click', () => {
        const theme = btn.getAttribute('data-bs-theme-value');
        setStoredTheme(theme);
        setTheme(theme);
        updateThemeUI(theme);
      });
    });

    // Keyboard shortcut: Ctrl+K or Cmd+K for search
    document.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const modal = document.getElementById('searchModal');
        if (modal) {
          const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
          bsModal.toggle();
        }
      }
    });
  });
})();
