/*
 * Account creation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals validateForm, serialize */

'use strict';

function createAccount() {
  var form = document.getElementById('profile_update');
  if (!validateForm(form)) {
    return;
  }
  var data = serialize(form);
  var email = document.getElementsByName('email1');

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.alert('Email Sent');
      window.location = '/index.php?Function=public';
    }
    else if (this.status == 401) {
      if (email.value) {
        window.alert('Account with the email \'' + email.value +
                     '\' already exists!');
      }
      email.value = '';
    }
    else if (this.status == 404) {
      if (email.value) {
        window.alert('Account Createion Failed.');
      }
      email.value = '';
    }
  };
  xhttp.open('POST', 'index.php?Function=create', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(data);
}
