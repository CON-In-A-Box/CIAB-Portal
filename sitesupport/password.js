/*
 * Base password functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals alertbox, confirmbox, basicBackendRequest */
/* exported changePassword, resetPassword */

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
