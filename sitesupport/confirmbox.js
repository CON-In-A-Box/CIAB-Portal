/*
 * Base Yes/No confirmation box
 */

/* jshint browser: true */
/* exported confirmbox */

var confirmboxPromise = (function() {
  'use strict';

  return {
    promise: function(title, message, params) {
      return new Promise(function(resolve, reject) {
        var dlg = document.getElementById('confirm_box');
        if (!dlg) {
          dlg = document.createElement('DIV');
          dlg.classList.add('UI-modal');
          dlg.id = 'confirm_box';
          var content = document.createElement('DIV');
          dlg.appendChild(content);
          content.classList.add('UI-modal-content');

          var h = document.createElement('H2');
          h.classList.add('UI-red');
          h.classList.add('UI-center');

          var s = document.createElement('SPAN');
          s.id = 'confirm_title';
          s.innerHTML = '';
          h.appendChild(s);
          content.appendChild(h);

          h = document.createElement('H3');
          h.classList.add('UI-center');
          s = document.createElement('SPAN');
          s.id = 'confirm_message';
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
          b1.innerHTML = 'Yes';
          b1.id = 'confirm_yes_button';
          form.appendChild(b1);

          var b2 = document.createElement('BUTTON');
          b2.classList.add('UI-button');
          b2.classList.add('UI-center');
          b2.classList.add('UI-border');
          b2.classList.add('UI-margin');
          b2.classList.add('UI-padding');
          b2.innerHTML = 'No';
          b2.id = 'confirm_no_button';
          form.appendChild(b2);

          document.body.appendChild(dlg);
        }

        var n = null;
        n = document.getElementById('confirm_title');
        n.innerHTML = title;
        n = document.getElementById('confirm_message');
        if (typeof message === 'undefined') {
          n.style.display = 'none';
        } else {
          n.innerHTML = message;
          n.style.display = 'inline';
        }

        document.getElementById('confirm_no_button').onclick = function() {
          reject(1);
          document.getElementById('confirm_box').style.display = 'none';
        };

        document.getElementById('confirm_yes_button').onclick = function() {
          resolve(1);
          document.getElementById('confirm_box').style.display = 'none';
        };

        if (params) {
          if ('yesMsg' in params) {
            n = document.getElementById('confirm_yes_button');
            n.innerHTML = params.yesMsg;
          } else {
            n.innerHTML = 'Yes';
          }
          if ('noMsg' in params) {
            n = document.getElementById('confirm_no_button');
            n.innerHTML = params.noMsg;
          } else {
            n.innerHTML = 'No';
          }
        }

        dlg.style.display = 'block';
      }
      );}
  };
}) ();

function confirmbox(title, message, params) {
  return confirmboxPromise.promise(title, message, params);
}
