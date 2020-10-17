/*
 * Account creation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals validateForm, showSpinner, hideSpinner, serialize, alertbox,
           apiRequest */
/* exported updateAccount */

'use strict';

function updateAccount(accountID) {
  var form = document.getElementById('profile_update');
  if (!validateForm(form)) {
    return;
  }
  var data = serialize(form);
  var method = 'POST';
  var uri = 'member';
  if (accountID > 0) {
    method = 'PUT';
    uri = 'member/' + accountID;
  }

  showSpinner();
  apiRequest(method, uri, data)
    .then(function() {
      hideSpinner();
      if (method == 'POST') {
        alertbox('Email Sent').then(function() {
          window.location = '/index.php?Function=public';
        });
      }
    })
    .catch(function(result) {
      hideSpinner();
      var email = document.getElementById('Email').value;
      if (result.status == 409) {
        if (email) {
          alertbox('Account with the email \'' + email +
                     '\' already exists!');
        } else {
          alertbox('Email for account invalid, please retry!');
        }
        document.getElementById('Email').value = '';
      }
      else if (result.status != 200) {
        if (email) {
          alertbox('Account Update Failed.');
        }
      }
    });
}
