/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, menubarElement */
/* exported */

function load() {
  apiRequest('GET', 'registration/admin', null)
    .then(function() {
      new menubarElement(
        {
          'text': 'Registration Admin',
          'function': 'registration/admin',
          'title': 'Registration Administration',
          'selectedStyle': 'event-color-primary',
          'baseStyle': 'UI-yellow',
          'icon': 'far fa-address-card'
        }
      ).createAdminElement();
    })
    .catch(function(response) {
      if (response instanceof Error) { throw response; }
    });

  apiRequest('GET', 'registration/ticket/list', null)
    .then(function(response) {
      var result = JSON.parse(response.responseText);
      var data = null;
      if (Array.isArray(result.data)) {
        data = result.data;
      } else {
        data = Array(result);
      }
      if (data.length > 0) {

        new menubarElement({
          'text': 'Manage Registrations',
          'function': 'registration/manage',
          'title': 'Manage Event Registrations',
          'selectedStyle': 'event-color-primary',
          'baseStyle': '',
          'icon': 'far fa-address-card'
        }).createElement();

        var eventName =  data[0].event.name;
        var generated = 0;
        data.forEach(function(ticket) {
          if (ticket.boarding_pass_generated) {
            generated += 1;
          }
        });
        if (generated < data.length) {
          apiRequest('GET', 'registration/open', null)
            .then(function(response) {
              result = JSON.parse(response.responseText);
              if (result.open) {
                new menubarElement({
                  'text': '<b>Checkin for <em>' + eventName + '</em></b>',
                  'function': 'registration/checkin',
                  'title': 'Registration Check-In',
                  'selectedStyle': 'event-color-primary',
                  'baseStyle': '',
                  'icon': 'fas fa-exclamation-circle'
                }).createElement();
              } else if (generated > 0 && generated < data.length) {
                new menubarElement({
                  'text': 'Checkin-Badge Pickup for <em>' + eventName + '</em>',
                  'function': 'registration/checkin',
                  'title': 'Registration Check-In',
                  'selectedStyle': 'event-color-primary',
                  'baseStyle': '',
                  'icon': 'fas fa-exclamation-circle'
                }).createElement();
              } else if (generated > 0) {
                new menubarElement({
                  'text': 'Report lost badge for <em>' + eventName + '</em>',
                  'function': 'registration/checkin',
                  'title': 'Registration Check-In',
                  'selectedStyle': 'event-color-primary',
                  'baseStyle': '',
                  'icon': 'fas fa-exclamation-circle'
                }).createElement();
              }
            })
        } else if (generated > 0) {
          new menubarElement({
            'text': 'Report lost badge for <em>' + eventName + '</em>',
            'function': 'registration/checkin',
            'title': 'Registration Check-In',
            'selectedStyle': 'event-color-primary',
            'baseStyle': '',
            'icon': 'fas fa-exclamation-circle'
          }).createElement();
        }
      }
    })
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
