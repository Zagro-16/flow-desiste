(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initAutoDismissAlerts();
    initConfirmActions();
    initSidebarAccordion();
    initTableQuickFilters();
  });

  function initAutoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alertEl) {
      setTimeout(function () {
        if (alertEl && alertEl.parentNode) {
          alertEl.classList.add('fade');
          alertEl.style.opacity = '0';
          setTimeout(() => alertEl.remove(), 350);
        }
      }, 4500);
    });
  }

  function initConfirmActions() {
    document.querySelectorAll('[data-confirm]').forEach(function (btn) {
      btn.addEventListener('click', function (event) {
        const msg = btn.getAttribute('data-confirm') || 'Confermi operazione?';
        if (!window.confirm(msg)) {
          event.preventDefault();
        }
      });
    });
  }

  function initSidebarAccordion() {
    const toggles = document.querySelectorAll('.ff-sidebar-toggle[data-ff-toggle]');
    toggles.forEach(function (btn) {
      const key = btn.getAttribute('data-ff-toggle');
      const panel = document.querySelector('.ff-sidebar-nav[data-ff-panel="' + key + '"]');
      if (!panel) {
        return;
      }

      const storageKey = 'ff-sidebar-' + key;
      const saved = localStorage.getItem(storageKey);
      const collapsed = saved === 'collapsed';
      panel.setAttribute('data-collapsed', collapsed ? 'true' : 'false');
      btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');

      btn.addEventListener('click', function () {
        const isCollapsed = panel.getAttribute('data-collapsed') === 'true';
        panel.setAttribute('data-collapsed', isCollapsed ? 'false' : 'true');
        btn.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');
        localStorage.setItem(storageKey, isCollapsed ? 'expanded' : 'collapsed');
      });
    });
  }

  function initTableQuickFilters() {
    document.querySelectorAll('[data-table-filter]').forEach(function (input) {
      const targetSel = input.getAttribute('data-table-filter');
      const rows = document.querySelectorAll(targetSel + ' tbody tr');
      if (!rows.length) {
        return;
      }

      input.addEventListener('input', function () {
        const q = (input.value || '').trim().toLowerCase();
        rows.forEach(function (row) {
          const txt = (row.innerText || '').toLowerCase();
          row.style.display = q === '' || txt.includes(q) ? '' : 'none';
        });
      });
    });
  }
})();
