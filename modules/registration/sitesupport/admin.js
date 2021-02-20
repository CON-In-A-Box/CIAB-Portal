/*
 * Javacript for the Registration page
 */

/* jshint browser: true */
/* jshint esversion: 9 */
/* jshint -W097 */
/* globals apiRequest, settingsTable */

import { settingsTable } from 'configuration.js';

var registrationAdminPage = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      'debug': true
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    debugmsg: function(message) {
      if (settings.debug) {
        var target = document.getElementById('headline_section');
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },

    load: function() {
      new settingsTable({api: 'registration/configuration'}).createElement();
    },

    updateSetting: function(element) {
      console.log('here');
      var id = element.id.replace(/_/g,' ');
      var type = element.type;
      var value;
      if (type == 'checkbox') {
        value = (element.checked ? '1' : '0');
      } else {
        value = element.value;
      }
      apiRequest('POST',
        'registration/configuration',
        'Field=' + id + '&Value=' + value)
        .then(function(response) {
          registrationAdminPage.debugmsg(response.responseText);
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          registrationAdminPage.debugmsg(response.responseText);
        });
    },

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', registrationAdminPage.load);
} else {
  window.attachEvent('onload', registrationAdminPage.load);
}
