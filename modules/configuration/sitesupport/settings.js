/*
 * Javacript for the Configuration page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner */

var settingsPage = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    addHeader: function() {
      var table = document.getElementById('settings');
      var row = document.createElement('DIV');
      table.appendChild(row);
      row.classList.add('UI-table-row');
      var div = document.createElement('DIV');
      row.appendChild(div);
      div.classList.add('UI-table-cell');
      div.innerHTML = 'Setting';
    },

    processSetting: function(input) {
      var table = document.getElementById('settings');
      var row = document.createElement('DIV');
      table.appendChild(row);
      row.classList.add('UI-table-row');
      var label = document.createElement('DIV');
      row.appendChild(label);
      label.classList.add('UI-table-cell');
      label.innerHTML = input.description;
      var val = document.createElement('INPUT');
      switch (input.fieldType) {
        case 'boolean':
          var isSet = (input.value == 'true' || input.value == '1');
          val.classList.add('UI-checkbox');
          val.type = 'checkbox';
          val.checked = isSet;
          break;
        case 'text':
          val.classList.add('UI-input');
          val.classList.add('UI-half');
          val.type = 'text';
          val.value = input.value;
          break;
        case 'integer':
          val.classList.add('UI-input');
          val.classList.add('UI-half');
          val.type = 'number';
          val.value = input.value;
          break;
        case 'select':
          val = document.createElement('SELECT');
          val.classList.add('UI-select');
          val.classList.add('UI-half');
          input.options.forEach(function(option) {
            var opt = document.createElement('OPTION');
            opt.text = option;
            if (input.value == option) {
              opt.selected = true;
            }
            val.add(opt);
          });
          break;
      }
      label.appendChild(val);
      val.classList.add('UI-right');
      val.id = input.field;
      val.onchange = settingsPage.onChange;
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
          if (data.data.length > 0) {
            document.getElementById('settings').innerHTML = '';
            settingsPage.addHeader();
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
