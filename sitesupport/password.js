/*
 * Base password functions
 */

/* jshint browser: true */
/* jshint -W097 */

'use strict';

function changePassword() {
  var current = document.getElementById('ciab_currentPassword');
  var newPassword = document.getElementById('ciab_newPassword');
  var again = document.getElementById('ciab_againPassword');
  if (!current.value) {
    window.alert('Current Password not supplied');
    return;
  }
  if (!newPassword.value) {
    window.alert('New Password not supplied');
    return;
  }
  if (!again.value || again.value != newPassword.value) {
    window.alert('New Password confirmation does not match');
    return;
  }
  if (!window.confirm('Proceed in changing your password?')) {
    current.value = '';
    newPassword.value = '';
    return;
  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.alert('Password Updated');
      location.reload();
    }
    else if (this.status == 403) {
      if (current.value) {
        window.alert('Current Password Incorrect');
      }
      current.value = '';
    }
  };
  xhttp.open('POST', 'index.php?Function=profile', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('auth=' + current.value + '&password=' + newPassword.value);
}

function resetPassword() {
  var email = document.getElementById('email');

  if (!email.value) {
    window.alert('Please enter your e-mail address to reset your password.');
    return;
  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.alert('Email Sent');
      window.location = '/index.php?Function=public';
    }
    else if (this.status == 404) {
      if (email.value) {
        window.alert('Account for ' + email.value + ' was not found.');
      }
      email.value = '';
    }
  };
  xhttp.open('POST', 'index.php?Function=recovery', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('password_reset=' + email.value);
}
