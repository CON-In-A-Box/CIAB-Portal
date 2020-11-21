/*
 * Javacript for the Configuration page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, configElement, configHeader */

var settingsPage = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    processSetting: function(input) {
      var table = document.getElementById('settings');
      var row = new configElement(
        {'onChange' : settingsPage.onChange}).createElement(input);
      table.appendChild(row);
    },

    onChange: function(e) {
      var value = e.target.value;
      if (e.target.type == 'checkbox') {
        value = e.target.checked;
      }
      showSpinner();
      apiRequest('PUT','member/current/configuration',
        'Field=' + e.target.id + '&Value=' + value)
        .then(function() {
          hideSpinner();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        });
    },

    load: function() {
      showSpinner();
      apiRequest('GET',
        'member/current/configuration', 'maxResults=all')
        .then(function(response) {
          hideSpinner();
          var data = JSON.parse(response.responseText);
          var table = document.getElementById('settings');
          table.innerHTML = '';
          table.appendChild(new configHeader().createElement())
          if (data.type == 'configuration') {
            settingsPage.processSetting(data);
          } else if (data.data.length > 0) {
            data.data.forEach(settingsPage.processSetting);
          }
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        })
    }

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', settingsPage.load);
} else {
  window.attachEvent('onload', settingsPage.load);
}
