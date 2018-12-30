/*
 * Javacript for Kiosk mode
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals kioskMode */

var kioskBase;

function switchKiosk() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      setTimeout(function() {location.reload() ;}, 1000);
    }
  };
  xhttp.open('POST', 'index.php?Function=' + kioskBase + '/kiosk', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('&toggleKiosk=true');
}

function failKiosk(error) {
  document.getElementById('kiosk_slider').checked = true;
  if (error) {
    window.alert('Login Failed (' + error + ')');
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
