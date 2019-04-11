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
    dlg.classList.add('w3-modal');
    dlg.id = 'refresh_spinner';
    var content = document.createElement('DIV');
    dlg.appendChild(content);
    content.classList.add('w3-modal-content');
    content.classList.add('w3-jumbo');
    content.classList.add('UI-center');
    content.classList.add('w3-round-xxlarge');
    content.style.width = '90px';
    content.style.backgroundColor = 'orange';
    var span = document.createElement('SPAN');
    span.innerHTML = '<i class="fa fa-refresh w3-spin"></i>';
    content.appendChild(span);
    document.body.appendChild(dlg);
  }
  dlg.style.display = 'block';
}

function hideSpinner() {
  document.getElementById('refresh_spinner').style.display = 'none';
}
