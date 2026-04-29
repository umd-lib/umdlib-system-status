(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.systemStatusBehavior = {
    attach: function (context, settings) {
      var systemStatusUrl = drupalSettings.system_status.system_status_url;

      function retrieveStatus(systemStatusUrl) {
        const statusContainer = context.querySelector('.navigation__utility_system-status-item');

        if (!statusContainer) {
          return;
        }

        fetch(systemStatusUrl)
          .then(response => response.json())
          .then(data => {
            if (!data || data['error']) {
              console.log('Error retrieving system status!');
              return;
            }

            const nonNormalCount = data['non_normal'];

            // Get or create parent list
            const parentList = statusContainer.parentElement;

            // Remove existing error items (all but the first operational item)
            const existingErrors = parentList.querySelectorAll('li.navigation__utility_system-status-item:not(:first-child)');
            existingErrors.forEach(item => item.remove());

            if (nonNormalCount > 0) {
              // Hide operational message
              statusContainer.style.display = 'none';

              // Add error items for each affected service
              data['non_normal_list'].forEach(service => {
                const errorItem = document.createElement('li');
                errorItem.className = 'navigation__utility_system-status-item s-stack-small t-body-small';
                errorItem.innerHTML = `
                  <div class="navigation__utility_system-status-item-status t-body-small">
                    <span class="navigation__utility_system-status-item-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="4" height="17" viewBox="0 0 4 17" fill="none">
                        <path d="M3.5 0H0V11.38H3.5V0Z" fill="#E21833"/>
                        <path d="M3.5 13.38H0V16.88H3.5V13.38Z" fill="#E21833"/>
                      </svg>
                    </span>
                    ${service}
                  </div>
                </li>`;
                parentList.appendChild(errorItem);
              });
            } else {
              // Show operational message, remove any error items
              statusContainer.style.display = '';
            }
          })
          .catch(error => console.error('Error fetching system status:', error));
      }

      // Simple once() implementation using data attribute
      const showSystemElement = context.querySelector('#showSystem');
      if (showSystemElement && !showSystemElement.dataset.systemStatusBehavior) {
        showSystemElement.dataset.systemStatusBehavior = 'processed';
        retrieveStatus(systemStatusUrl);
      }
    }
  };
})(Drupal, drupalSettings);

