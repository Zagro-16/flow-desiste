(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initAutoDismissAlerts();
    initConfirmActions();
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
})();
