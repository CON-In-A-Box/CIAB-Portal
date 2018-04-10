/*
 * Base functions for the registration module
 */

/* jshint browser: true */
/* jshint -W097 */

'use strict';

function refreshBadgeData(badge) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      window.alert('404!');
    }
    else if (this.status == 409) {
      window.alert('409!');
    }
  };
  showSpinner();
  xhttp.open('POST', 'index.php?Function=registration', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('refreshData=' + badge);
}
