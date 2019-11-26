/*
 * Javacript for Kiosk mode
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals kioskMode, checkAuthentication, userEmail, alertbox,
           basicBackendRequest */
/* exported toggleKioskMode */

var kioskBase;

function switchKiosk() {
  basicBackendRequest('POST', kioskBase + '/kiosk', 'toggleKiosk=true',
    function() {
      setTimeout(function() {location.reload() ;}, 1000);
    });
}

function failKiosk(error) {
  document.getElementById('kiosk_slider').checked = true;
  if (error) {
    alertbox('Login Failed (' + error + ')');
  }
}

function toggleKioskMode(base) {
  kioskBase = base;
  if (kioskMode) {
    checkAuthentication(userEmail, switchKiosk, failKiosk,
      {title: 'Exiting Kiosk Mode'});
  } else {
    setTimeout(function() {
      window.location = 'index.php?Function=' + base + '/kiosk';
    }, 1000);
  }
}
