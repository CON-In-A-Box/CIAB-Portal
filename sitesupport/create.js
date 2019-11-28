/*
 * Account creation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals validateForm, serialize, alertbox, basicBackendRequest */
/* exported createAccount */

'use strict';

function createAccount() {
  var form = document.getElementById('profile_update');
  if (!validateForm(form)) {
    return;
  }
  var data = serialize(form);

  basicBackendRequest('POST', 'create', data,
    function() {
      alertbox('Email Sent').then(function() {
        window.location = '/index.php?Function=public';
      });
    },
    function(result) {
      var email = document.getElementById('email1').value;
      if (result.status == 401) {
        if (email) {
          alertbox('Account with the email \'' + email +
                     '\' already exists!');
        } else {
          alertbox('Email for account invalid, please retry!');
        }
        document.getElementById('email1').value = '';
      }
      else if (result.status == 404) {
        if (email) {
          alertbox('Account Createion Failed.');
        }
        document.getElementById('email1').value = '';
      }
    });
}
