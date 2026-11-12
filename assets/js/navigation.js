(() => {
  'use strict';

  const header = document.querySelector('[data-site-header]');
  const toggle = header?.querySelector('[data-menu-toggle]');
  const panel = header?.querySelector('[data-menu-panel]');

  if (!header || !toggle || !panel) {
    return;
  }

  const closeMenu = (restoreFocus = false) => {
    toggle.setAttribute('aria-expanded', 'false');
    header.classList.remove('is-menu-open');

    if (restoreFocus) {
      toggle.focus();
    }
  };

  header.classList.add('is-nav-enhanced');
  toggle.hidden = false;

  toggle.addEventListener('click', () => {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isOpen));
    header.classList.toggle('is-menu-open', !isOpen);
  });

  panel.addEventListener('click', (event) => {
    if (event.target.closest('a')) {
      closeMenu();
    }
  });

  const desktopNavigation = window.matchMedia('(min-width: 48rem)');
  desktopNavigation.addEventListener('change', (event) => {
    if (event.matches) {
      closeMenu();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && header.classList.contains('is-menu-open')) {
      closeMenu(true);
    }
  });

  document.addEventListener('click', (event) => {
    if (header.classList.contains('is-menu-open') && !header.contains(event.target)) {
      closeMenu();
    }
  });
})();

