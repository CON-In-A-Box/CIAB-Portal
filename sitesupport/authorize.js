/*
 * Base for the OAuth2 Authentication process
 */

/* jshint browser: true */
/* jshint -W097 */
/* global apiRequest, showSpinner, hideSpinner
*/


var oauthAuthorize = (function() {
  'use strict';

  return {
    build: function() {
      showSpinner();
      apiRequest('GET', 'member/', null)
        .then(function(response)  {
          var result = JSON.parse(response.responseText);
          hideSpinner();
          document.getElementById('known_user').classList.remove('UI-hide');
          document.getElementById('username').classList.remove('UI-hide');
          document.getElementById('user').innerHTML = result.first_name +
            '&nbsp;' + result.last_name;
          document.getElementById('user_id').value = result.id;
          console.log(response);
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
          document.getElementById('login').classList.remove('UI-hide');
          console.log(response);
        });
    },

    loginUser: function() {
      document.getElementById('auth_bad').classList.add('UI-hide');
      var username = document.getElementById('login_user').value;
      var password = document.getElementById('login_password').value;
      showSpinner();
      apiRequest('POST', 'token', 'grant_type=password&username=' +
             username + '&password=' + password + '&client_id=ciab')
        .then(function(response) {
          hideSpinner();
          localStorage.setItem('ciab_apiAuthorization', response.responseText);
          document.getElementById('login').classList.add('UI-hide');
          console.log(response);
          oauthAuthorize.build();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
          console.log(response);
          document.getElementById('auth_bad').classList.remove('UI-hide');
        });
    }
  };
})();

if (window.addEventListener) {
  window.addEventListener('load', oauthAuthorize.build);
} else {
  window.attachEvent('onload', oauthAuthorize.build);
}
