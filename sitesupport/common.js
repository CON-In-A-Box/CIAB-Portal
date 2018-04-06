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
