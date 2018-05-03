/*
 * Account creation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals validateForm, serialize */
/* exported createAccount */

'use strict';

function createAccount() {
  var form = document.getElementById('profile_update');
  if (!validateForm(form)) {
    return;
  }
  var data = serialize(form);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.alert('Email Sent');
      window.location = '/index.php?Function=public';
    }
    else if (this.readyState == 4 && this.status == 401) {
      var email = document.getElementById('email1').value;
      if (email) {
        window.alert('Account with the email \'' + email +
                     '\' already exists!');
      } else {
        window.alert('Email for account invalid, please retry!');
      }
      document.getElementById('email1').value = '';
    }
    else if (this.status == 404) {
      if (email.value) {
        window.alert('Account Createion Failed.');
      }
      document.getElementById('email1').value = '';
    }
  };
  xhttp.open('POST', 'index.php?Function=create', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(data);
}
