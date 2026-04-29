(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.systemStatusBehavior = {
    attach: function (context, settings) {

      var systemStatusUrl = drupalSettings.system_status.system_status_url;
      var utilityNavItem = context.querySelector('.utility-nav-systems-status');

      /**
       * Retrieves the systems status from the given endpoint, and updates
       * the "Systems Status" entry in the utility navigation menu, and the
       * system status block.
       */
      function retrieveStatus(systemStatusUrl) {
        var utilityNavItemStatus = context.querySelector('.utility-nav-systems-status > .status');
        var systemStatusDate = context.querySelector('.systems-status-date');
        var systemStatusOperational = context.querySelector('.systems-status-operational > .status');
        var systemStatusProblem = context.querySelector('.systems-status-maintenance > .status');

        if (!utilityNavItem) {
          // Status menu item not on page
          return;
        }

        systemStatusDate.innerHTML = getFormattedDate();

        fetch(systemStatusUrl)
          .then(response => response.json())
          .then(data => {
            if (!data) {
              return;
            }
            if (data['error']) {
              console.log('Error retrieving system status!')
              return;
            }

            var nonNormalCount = data['non_normal'];
            document.getElementById('systems-status-spinner').style.display = 'none';
            document.getElementById('systems-status-content').style.display = '';
            document.getElementById('systems-status-spinner-mobile').style.display = 'none';
            document.getElementById('systems-status-content-mobile').style.display = '';
            
            if (nonNormalCount > 0) {
              var nonNormalCaption = '<span class="badge">' + nonNormalCount + '</span>';
              utilityNavItemStatus.innerHTML = nonNormalCaption;
              document.getElementById('system-no-issues').style.display = 'none';
              document.getElementById('system-issues').style.display = '';
              document.getElementById('system-no-issues-mobile').style.display = 'none';
              document.getElementById('system-issues-mobile').style.display = '';
            } else {
              document.getElementById('system-issues').style.display = 'none';
              document.getElementById('system-no-issues').style.display = '';
              document.getElementById('system-issues-mobile').style.display = 'none';
              document.getElementById('system-no-issues-mobile').style.display = '';
            }
            var problemHtml = "<ol>"
            data['non_normal_list'].forEach(element => problemHtml += `<li>${element}</li>`);
            problemHtml += "</ol>"
            systemStatusProblem.innerHTML = problemHtml;
          })
          .catch(error => console.error('Error fetching system status:', error));
      }

      // Simple once() implementation using data attribute
      var showSystemElement = context.querySelector('#showSystem');
      if (showSystemElement && !showSystemElement.dataset.systemStatusBehavior) {
        showSystemElement.dataset.systemStatusBehavior = 'processed';
        retrieveStatus(systemStatusUrl);
      }

      function getFormattedDate(date) {
        if (date == undefined) {
          date = new Date();
        }
        let mo = new Intl.DateTimeFormat('en', { month: 'long' }).format(date);
        let da = new Intl.DateTimeFormat('en', { day: 'numeric' }).format(date);
        let ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
        let time = new Intl.DateTimeFormat('en', { hour: 'numeric', minute: 'numeric' }).format(date);
        return `${mo} ${da}, ${ye} at ${time}`;
      }
    }
  };
})(Drupal, drupalSettings);

