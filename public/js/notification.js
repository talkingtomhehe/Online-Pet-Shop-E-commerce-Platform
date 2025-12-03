document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('notification-toggle');
  var dropdown = document.getElementById('notification-dropdown');
  var badge = document.getElementById('notification-badge');

  // Toggle Dropdown
  if (toggle) {
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      e.preventDefault(); // Prevent jump to top
      if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
      }
    });
  }

  // Close when clicking outside
  document.addEventListener('click', function () {
    if (dropdown) dropdown.style.display = 'none';
  });

  // Stop clicks inside dropdown from closing it
  if (dropdown) {
    dropdown.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }

  // Handle clicking a notification item
  var items = document.querySelectorAll('.notification-item');
  items.forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var notificationId = this.getAttribute('data-id');
      var href = this.getAttribute('href');

      // Use the global SITE_URL variable defined in header
      // If SITE_URL is not defined in JS, fallback to root '/'
      var baseUrl = typeof SITE_URL !== 'undefined' ? SITE_URL : '/';

      var xhr = new XMLHttpRequest();
      xhr.open('POST', baseUrl + 'ajax/mark-notification-read', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          // Navigate regardless of success/fail so user isn't stuck
          window.location.href = href;
        }
      };
      xhr.send('id=' + encodeURIComponent(notificationId));
    });
  });

  // Handle "Mark All Read"
  var markAllBtn = document.getElementById('mark-all-read');
  if (markAllBtn) {
    markAllBtn.addEventListener('click', function (e) {
      e.preventDefault();

      var baseUrl = typeof SITE_URL !== 'undefined' ? SITE_URL : '/';

      var xhr = new XMLHttpRequest();
      xhr.open('POST', baseUrl + 'ajax/mark-notification-read', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (badge) badge.style.display = 'none';
          // Visually dim items
          var items = document.querySelectorAll('.notification-item');
          items.forEach(function (it) {
            it.style.opacity = 0.6;
          });
        }
      };
      xhr.send('id=0');
    });
  }
});
