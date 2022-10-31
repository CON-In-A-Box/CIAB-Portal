/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner */
/* exported settingsTable */

var configElement = function(options) {
  'use strict';

  return {
    settings: options,

    options: function(opts) {
      this.settings = Object.assign(this.settings, opts);
    },

    debugmsg: function(message) {
      if (this.settings.debugElement) {
        var target = document.getElementById(this.settings.debugElement);
        if (target) {
          target.classList.add('UI-show');
          target.innerHTML = message;
        }
      }
    },

    createElement: function(input) {
      var row = document.createElement('DIV');
      row.classList.add('UI-table-row');
      var label = document.createElement('DIV');
      row.appendChild(label);
      label.classList.add('UI-table-cell');
      if (input.description) {
        label.innerHTML = input.description;
      } else {
        label.innerHTML = input.field;
      }
      var val = document.createElement('INPUT');
      switch (input.fieldType) {
        case 'boolean':
          var isSet = (input.value == 'true' || input.value == '1');
          val.classList.add('UI-checkbox');
          val.type = 'checkbox';
          val.checked = isSet;
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
        default:
          val.classList.add('UI-input');
          val.classList.add('UI-half');
          val.type = 'text';
          val.value = input.value;
          break;
      }
      label.appendChild(val);
      val.classList.add('UI-right');
      val.id = input.field;
      if (this.settings && 'onChange' in this.settings) {
        val.onchange = this.settings.onChange;
      }
      return row;
    },

  };
}


var configHeader = function(options) {
  'use strict';

  return {
    settings: options,

    options: function(opts) {
      this.settings = Object.assign(this.settings, opts);
    },

    debugmsg: function(message) {
      if (this.settings.debugElement) {
        var target = document.getElementById(this.settings.debugElement);
        if (target) {
          target.classList.add('UI-show');
          target.innerHTML = message;
        }
      }
    },

    createElement: function() {
      var row = document.createElement('DIV');
      row.classList.add('UI-table-row');
      var div = document.createElement('DIV');
      row.appendChild(div);
      div.classList.add('UI-table-cell');
      div.innerHTML = 'Setting';
      return row;
    },

  };
}


var settingsTable = function(options) {
  'use strict';

  return {
    settings: Object.assign({
      api: undefined,
      onChange: undefined,
      element: 'settings'
    }, options),

    options: function(opts) {
      this.settings = Object.assign(this.settings, opts);
    },

    debugmsg: function(message) {
      if (this.settings.debugElement) {
        var target = document.getElementById(this.settings.debugElement);
        if (target) {
          target.classList.add('UI-show');
          target.innerHTML = message;
        }
      }
    },

    processSetting: function(input) {
      var obj = this;
      var onChange = obj.settings.onChange;
      if (onChange === undefined) {
        onChange = obj.onChange;
      }
      var table = document.getElementById(this.settings.element);
      var row = new configElement(
        {'onChange' : onChange.bind(obj)}).createElement(input);
      table.appendChild(row);
    },


    onChange: function(e) {
      var value = e.target.value;
      if (e.target.type == 'checkbox') {
        value = e.target.checked;
      }
      showSpinner();
      apiRequest('PUT', this.settings.api,
        'Field=' + e.target.id + '&Value=' + value)
        .then(function() {
          hideSpinner();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        });
    },

    createElement: function() {
      var obj = this;
      showSpinner();
      apiRequest('GET', this.settings.api, 'max_results=all')
        .then(function(response) {
          hideSpinner();
          var data = JSON.parse(response.responseText);
          var table = document.getElementById(obj.settings.element);
          table.innerHTML = '';
          table.appendChild(new configHeader().createElement())
          if (data.type == 'configuration') {
            obj.processSetting(data);
          } else {
            if (typeof data.data == 'object') {
              if (Object.keys(data.data).length > 0) {
                for (const item in data.data) {
                  obj.processSetting(data.data[item]);
                }
              }
            } else {
              if (data.data.length > 0) {
                data.data.forEach(function(item) {obj.processSetting(item)});
              }
            }
          }
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        })
    }

  };
}
