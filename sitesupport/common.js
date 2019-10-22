/*
 * Javacript for the site
 */

/* jshint browser: true */
/* jshint -W097 */

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
    span.innerHTML = '<i class="fa fa-refresh UI-spin"></i>';
    content.appendChild(span);
    document.body.appendChild(dlg);
  }
  dlg.style.display = 'block';
}

function hideSpinner() {
  document.getElementById('refresh_spinner').style.display = 'none';
}

function urlsafe_b64encode(data) {
    return btoa(data).replace(/\+/g, '-').replace(/\//g, '_');
}

function urlsafe_b64decode(data) {
    return atob(data.replace(/-/g, '+').replace(/_/g, '/'));
}
