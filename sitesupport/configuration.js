/* jshint browser: true */
/* jshint -W097 */
/* exported configElement, configHeader */

'use strict'

var configElement = function(options) {

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
