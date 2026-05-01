(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.systemStatusBehavior = {
    attach: function (context, settings) {
      var systemStatusUrl = drupalSettings.system_status.system_status_url;

      function retrieveStatus(systemStatusUrl) {
        const statusContainer = context.querySelector('#showSystem .navigation__utility_system-status-item');

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

            if (typeof data['non_normal'] === 'undefined' || typeof data['non_normal_list'] === 'undefined') {
              console.error('Invalid system status response structure: missing non_normal or non_normal_list');
              return;
            }

            const nonNormalCount = data['non_normal'];

            // Target the showSystem container directly
            const statusList = context.querySelector('#showSystem');

            console.log('Status list element:', statusList);

            // Update badge with count and appropriate status class
            const badge = context.querySelector('.badge');
            if (badge) {
              badge.textContent = nonNormalCount;
              badge.classList.remove('badge--error', 'badge--ok');
              badge.classList.add(nonNormalCount > 0 ? 'badge--error' : 'badge--ok');
            }

            // Format and update current date and time
            const now = new Date();
            const formattedDateTime = now.toLocaleDateString('en-US', {
              month: 'long',
              day: 'numeric',
              year: 'numeric'
            }) + ' at ' + now.toLocaleTimeString('en-US', {
              hour: 'numeric',
              minute: '2-digit',
              hour12: true
            });

            const dateTimeDiv = context.querySelector('#systemStatusDateTime');
            if (dateTimeDiv) {
              dateTimeDiv.textContent = formattedDateTime;
            }

            const statusSection = context.querySelector('#systemStatus');
            if (statusSection) {
              statusSection.setAttribute('aria-label', formattedDateTime);
            }

            // Remove existing error items (all but the first operational item)
            const existingErrors = statusList.querySelectorAll('li.navigation__utility_system-status-item:not(:first-child)');
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
                statusList.appendChild(errorItem);
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

