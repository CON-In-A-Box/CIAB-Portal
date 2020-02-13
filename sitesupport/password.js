/*
 * Base password functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals alertbox, confirmbox, basicBackendRequest, apiRequest,
           showSpinner, hideSpinner */
/* exported changePassword, resetPassword, loginUser */

'use strict';

function changePassword() {
  var current = document.getElementById('ciab_currentPassword');
  var newPassword = document.getElementById('ciab_newPassword');
  var again = document.getElementById('ciab_againPassword');
  if (!current.value) {
    alertbox('Current Password not supplied');
    return;
  }
  if (!newPassword.value) {
    alertbox('New Password not supplied');
    return;
  }
  if (!again.value || again.value != newPassword.value) {
    alertbox('New Password confirmation does not match');
    return;
  }

  confirmbox('Proceed in changing your password?').then(function() {
    basicBackendRequest('POST', 'profile',
      'auth=' + current.value + '&password=' + newPassword.value,
      function() {
        alertbox('Password Updated').then(
          function() {
            location.reload();
          }
        );
      },
      function(response) {
        if (response.status == 403) {
          if (current.value) {
            alertbox('Current Password Incorrect');
          }
          current.value = '';
        }
      });
  },
  function() {
    current.value = '';
    newPassword.value = '';
    again.value = '';
  });
}

function resetPassword() {
  var email = document.getElementById('email');

  if (!email.value) {
    alertbox('Please enter your e-mail address to reset ' +
                     'your password.');
    return;
  }

  basicBackendRequest('POST', 'recovery', 'password_reset=' + email.value,
    function() {
      alertbox('Email Sent').then(function() {
        window.location = '/index.php?Function=public';
      });
    },
    function() {
      if (email.value) {
        alertbox('Account for ' + email.value + ' was not found.');
      }
      email.value = '';
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
