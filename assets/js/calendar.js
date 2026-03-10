(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('calendarSearch');
    if (!searchInput) {
      return;
    }

    searchInput.addEventListener('input', function () {
      const q = (searchInput.value || '').trim().toLowerCase();
      document.querySelectorAll('.calendar-item').forEach(function (el) {
        const haystack = (el.getAttribute('data-search') || '').toLowerCase();
        const visible = q === '' || haystack.includes(q);
        el.style.display = visible ? '' : 'none';
      });
    });
  });
})();
