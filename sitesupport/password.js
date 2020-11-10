/*
 * Base password functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals alertbox, apiRequest, showSpinner, hideSpinner */
/* exported resetPassword, loginUser, newPassword */

'use strict';

function resetPassword() {
  var email = document.getElementById('email');

  if (!email.value) {
    alertbox('Please enter your e-mail address to reset ' +
                     'your password.');
    return;
  }

  showSpinner();
  apiRequest('POST', 'member/' + email.value + '/password', '')
    .then(function() {
      hideSpinner();
      alertbox('Email Sent').then(function() {
        window.location = '/index.php?Function=public';
      });
    })
    .catch(function() {
      hideSpinner();
      if (email.value) {
        alertbox('Password reset for ' + email.value + ' failed.');
      }
      email.value = '';
    });
}


function newPassword() {
  var email = document.getElementById('email');
  var code = document.getElementById('authorization');
  var pass = document.getElementById('new_password');
  var verify = document.getElementById('confirm_password');

  if (!pass.value) {
    alertbox('Please enter your new password.');
    return;
  }

  if (pass.value !== verify.value) {
    alertbox('Password and confirmation do not match.');
    return;
  }

  showSpinner();
  apiRequest('PUT', 'member/' + email.value + '/password/recovery',
    'OneTimeCode=' + code.value + '&NewPassword=' + pass.value)
    .then(function() {
      hideSpinner();
      alertbox('Password Reset.').then(function() {
        window.location = '/index.php?Function=public';
      });
    })
    .catch(function(response) {
      hideSpinner();
      if (response instanceof Error) { throw response; }
      if (email.value) {
        alertbox('Password reset for ' + email.value + ' failed:' +
                 response.responseText);
      }
    });
}

function loginUser(userEntry, passwordEntry)
{
  var username = document.getElementsByName(userEntry)[0].value;
  var password = document.getElementsByName(passwordEntry)[0].value;
  document.getElementById('auth_locked').classList.add('UI-hide');
  document.getElementById('auth_bad').classList.add('UI-hide');
  document.getElementById('auth_duplicate').classList.add('UI-hide');
  showSpinner();
  apiRequest('POST', 'token', 'grant_type=password&username=' +
             username + '&password=' + password + '&client_id=ciab')
    .then(function(result) {
      localStorage.setItem('ciab_apiAuthorization', result.responseText);
      var myxhttp = new XMLHttpRequest();
      myxhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          hideSpinner();
          if (this.status == 200) {
            document.body.innerHTML = this.responseText;
            showSpinner();
            window.location = '/index.php?Function=public';
          }
        }
      };
      var auth = JSON.parse(result.responseText);
      myxhttp.open('GET', window.location);
      myxhttp.setRequestHeader('x-ciab-api',
        auth.token_type + ' ' + auth.access_token);
      myxhttp.send();
    })
    .catch(function() {
      apiRequest('GET', 'member/' + username + '/status' , null)
        .then(function(result) {
          var failure = JSON.parse(result.responseText);
          if (failure.status == 0x02) {
            window.location = '/index.php?Function=recovery&expired=1';
          }
          if (failure.status == 0x03) {
            document.getElementById('auth_locked').classList.remove('UI-hide');
          }
          else if (failure.status == 0x10) {
            document.getElementById('auth_duplicate').classList
              .remove('UI-hide');
          } else {
            document.getElementById('auth_bad').classList.remove('UI-hide');
          }
          hideSpinner();
        })
        .catch(function() {
          hideSpinner();
          document.getElementById('auth_bad').classList.remove('UI-hide');
        });
    });
}
