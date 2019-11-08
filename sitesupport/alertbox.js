/*
 * Base OK alert box
 */

/* jshint browser: true */
/* exported alertbox */

var alertboxPromise = (function() {
  'use strict';

  return {
    promise: function(title, message, params) {
      return new Promise(function(resolve) {
        var dlg = document.getElementById('alert_box');
        if (!dlg) {
          dlg = document.createElement('DIV');
          dlg.classList.add('UI-modal');
          dlg.id = 'alert_box';
          var content = document.createElement('DIV');
          dlg.appendChild(content);
          content.classList.add('UI-modal-content');

          var h = document.createElement('H2');
          h.classList.add('UI-red');
          h.classList.add('UI-center');

          var s = document.createElement('SPAN');
          s.id = 'alert_title';
          s.innerHTML = '';
          h.appendChild(s);
          content.appendChild(h);

          h = document.createElement('H3');
          h.classList.add('UI-center');
          s = document.createElement('SPAN');
          s.id = 'alert_message';
          s.innerHTML = '';
          h.appendChild(s);
          content.appendChild(h);

          var form = document.createElement('DIV');
          form.classList.add('UI-center');
          content.appendChild(form);

          var b1 = document.createElement('BUTTON');
          b1.classList.add('UI-button');
          b1.classList.add('UI-center');
          b1.classList.add('UI-border');
          b1.classList.add('UI-margin');
          b1.classList.add('UI-padding');
          b1.innerHTML = 'OK';
          b1.id = 'alert_ok_button';
          form.appendChild(b1);

          document.body.appendChild(dlg);
        }

        var n = null;
        n = document.getElementById('alert_title');
        n.innerHTML = title;
        n = document.getElementById('alert_message');
        if (typeof message !== 'undefined') {
          n.style.display = 'block';
          n.innerHTML = message;
        } else {
          n.style.display = 'none';
        }

        n = document.getElementById('alert_ok_button');
        n.onclick = function() {
          document.getElementById('alert_box').style.display = 'none';
          resolve(1);
        };

        if (params) {
          this.params = params;
          if ('OkMsg' in params) {
            n = document.getElementById('alert_ok_button');
            n.innerHTML = params.OkMsg;
          } else {
            n.innerHTML = 'OK';
          }
        }

        dlg.style.display = 'block';
      }
      );}
  };
}) ();

function alertbox(title, message, params) {
  return alertboxPromise.promise(title, message, params);
}
