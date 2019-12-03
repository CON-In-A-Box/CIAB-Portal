/*
 * Javacript for the site
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals alertbox */
/* exported escapeHtml, showSpinner, hideSpinner, urlsafeB64Encode,
            urlsafeB64Decode, basicBackendRequest */

'use strict';

function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    '\'': '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function showSpinner() {
  var dlg = document.getElementById('refresh_spinner');
  if (!dlg) {
    dlg = document.createElement('DIV');
    dlg.classList.add('UI-modal');
    dlg.id = 'refresh_spinner';
    var content = document.createElement('DIV');
    dlg.appendChild(content);
    content.classList.add('UI-spinner-div');
    content.style.width = '90px';
    content.style.backgroundColor = 'orange';
    var span = document.createElement('SPAN');
    span.innerHTML = '<i class="fas fa-sync UI-spin"></i>';
    content.appendChild(span);
    document.body.appendChild(dlg);
  }
  dlg.style.display = 'block';
}

function hideSpinner() {
  document.getElementById('refresh_spinner').style.display = 'none';
}

function urlsafeB64Encode(data) {
  return btoa(data).replace(/\+/g, '-').replace(/\//g, '_');
}

function urlsafeB64Decode(data) {
  return atob(data.replace(/-/g, '+').replace(/_/g, '/'));
}

function basicBackendRequest(method, target, parameter, success, failure) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      success(this);
      hideSpinner();
    } else if (this.readyState == 4) {
      if (typeof failure !== 'undefined') {
        failure(this);
        hideSpinner();
      } else {
        hideSpinner();
        alertbox(this.status);
      }
    }
  };
  showSpinner();
  var url = 'index.php?Function=' + target;
  if (method == 'POST') {
    xhttp.open(method, url, true);
    if (parameter instanceof FormData) {
      xhttp.setRequestHeader('enctype', 'multipart/form-data');
    } else {
      xhttp.setRequestHeader('Content-type',
        'application/x-www-form-urlencoded');
    }
    xhttp.send(parameter);
  } else {
    xhttp.open('GET', url + '&' + parameter, true);
    xhttp.send();
  }

}
