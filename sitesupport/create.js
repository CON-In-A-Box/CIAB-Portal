/*
 * Account creation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals validateForm, serialize, alertbox */
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
    var email = document.getElementById('email1').value;
    if (this.readyState == 4 && this.status == 200) {
      alertbox('Email Sent').then(function() {
        window.location = '/index.php?Function=public';
      });
    }
    else if (this.readyState == 4 && this.status == 401) {
      if (email) {
        alertbox('Account with the email \'' + email +
                     '\' already exists!');
      } else {
        alertbox('Email for account invalid, please retry!');
      }
      document.getElementById('email1').value = '';
    }
    else if (this.status == 404) {
      if (email) {
        alertbox('Account Createion Failed.');
      }
      document.getElementById('email1').value = '';
    }
  };
  xhttp.open('POST', 'index.php?Function=create', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(data);
}
