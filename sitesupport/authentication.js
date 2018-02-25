/*
 * Base function to check authentication of a user
 */

/* jshint browser: true */

'use strict';

var onSuccess = null;
var onFail = null;
var user = null;

function _hideAuthentication() {
  document.getElementById('reauthentication_dlg').style.display = 'none';
}

function _cancelAuthentication() {
  _hideAuthentication();
  if (onFail) {
    onFail();
  }
}

function _checkKey(event) {
  if (event.keyCode === 13) {
    _hideAuthentication();
    var pass = document.getElementById('password_input').value;
    var target = document.getElementById('target').value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          if (onSuccess) {
            onSuccess();
          }
          return;
        }
        else if (this.readyState == 4 && this.status != 200) {
          if (onFail) {
            onFail(this.status);
          }
          return;
        }
      };
    if (!target) {
      xhttp.open('POST', 'index.php?Function=functions', true);
    } else {
      xhttp.open('POST', 'index.php?Function=' + target, true);
    }
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('validate_login=' + user + '&validate_passwd=' +
               encodeURI(pass));
  }
}

function checkAuthentication(username, success, failure, params) {
  var dlg = document.getElementById('reauthentication_dlg');
  onSuccess = success;
  onFail = failure;
  user = username;
  if (!dlg) {
    dlg = document.createElement('DIV');
    dlg.classList.add('w3-modal');
    dlg.id = 'reauthentication_dlg';
    var content = document.createElement('DIV');
    dlg.appendChild(content);
    content.classList.add('w3-modal-content');

    var x = document.createElement('SPAN');
    x.onclick = _cancelAuthentication;
    x.classList.add('w3-button');
    x.classList.add('w3-display-topright');
    x.innerHTML = '&times;';
    content.appendChild(x);

    var h = document.createElement('H2');
    h.classList.add('w3-red');
    h.classList.add('w3-center');
    var t;
    if (params && ('title' in params)) {
      t = document.createTextNode(params.title);
    } else {
      t = document.createTextNode('Entering Admin Mode');
    }
    h.appendChild(t);
    content.appendChild(h);
    var r = document.createElement('HR');
    content.appendChild(r);

    h = document.createElement('H3');
    h.classList.add('w3-center');
    t = document.createTextNode('Verify password for ' + username);
    h.appendChild(t);
    content.appendChild(h);

    var form = document.createElement('DIV');
    form.classList.add('w3-center');
    content.appendChild(form);

    var i = document.createElement('INPUT');
    i.classList.add('w3-center');
    i.classList.add('w3-input');
    i.classList.add('w3-border');
    i.classList.add('w3-margin');
    i.classList.add('w3-padding');
    i.setAttribute('type', 'password');
    i.id = 'password_input';
    i.style.width = '95%';
    i.onkeyup = _checkKey;
    form.appendChild(i);

    i = document.createElement('INPUT');
    i.classList.add('w3-hide');
    i.id = 'target';
    i.value = null;
    if (params && ('target' in params)) {
      i.value = params.target;
    }
    form.appendChild(i);

    r = document.createElement('HR');
    content.appendChild(r);

    document.body.appendChild(dlg);
  } else {
    document.getElementById('password_input').value = '';
  }

  dlg.style.display = 'block';
}
