/*
 * Javacript for Console mode
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals consoleMode, checkAuthentication, userEmail, alertbox,
           basicBackendRequest */
/* exported toggleConsoleMode */

var consoleBase;

function switchConsole() {
  basicBackendRequest('POST', consoleBase + '/console', 'toggleConsole=true',
    function() {
      setTimeout(function() {location.reload() ;}, 1000);
    });
}

function failConsole(error) {
  document.getElementById('console_slider').checked = true;
  if (error) {
    alertbox('Login Failed (' + error + ')');
  }
}

function toggleConsoleMode(base) {
  consoleBase = base;
  if (consoleMode) {
    checkAuthentication(userEmail, switchConsole, failConsole,
      {title: 'Exiting Console Mode'});
  } else {
    setTimeout(function() {
      window.location = 'index.php?Function=' + base + '/console';
    }, 1000);
  }
}
