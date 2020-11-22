/*
 * Javacript for the Site Configuration page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals settingsTable */

function load() {
  new settingsTable({api: 'admin/configuration'}).createElement();
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
