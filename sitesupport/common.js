/*
 * Javacript for the site
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals alertbox */
/* exported escapeHtml, showSpinner, hideSpinner, urlsafeB64Encode, progressSpinner,
            urlsafeB64Decode, basicBackendRequest, apiRequest, apiRefresh, systemDebug,
            simpleObjectToRequest */

'use strict';

var systemDebug = false;

function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    '\'': '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function simpleObjectToRequest(obj) {
  return Object.entries(obj)
    .map(([key, value]) => (`${key}=${value}`))
    .join('&');
}

function showSpinner(progress = 0) {
  var id = 'refresh_spinner';
  if (progress > 0) {
    id = 'refresh_spinner_progress'
  }
  var dlg = document.getElementById(id);
  if (!dlg) {
    dlg = document.createElement('DIV');
    dlg.classList.add('UI-modal');
    dlg.id = id;
    var content = document.createElement('DIV');
    dlg.appendChild(content);
    content.classList.add('UI-spinner-div');
    content.style.width = '90px';
    content.style.backgroundColor = 'orange';
    var span = document.createElement('SPAN');
    span.innerHTML = '<i class="fas fa-sync UI-spin"></i>';
    content.appendChild(span);
    if (progress > 0) {
      var prog = document.createElement('PROGRESS');
      prog.style.width = '90px';
      prog.style.height = '20px';
      prog.style.position = 'absolute';
      prog.style.marginLeft = '-76px';
      prog.style.marginTop = '37px';
      prog.id = 'refresh_spinner_progress_bar';
      content.appendChild(prog);
    }
    document.body.appendChild(dlg);
  }
  if (progress > 0) {
    var prog2 = document.getElementById('refresh_spinner_progress_bar');
    prog2.setAttribute('max', progress);
    prog2.value = 0;
  }
  dlg.style.display = 'block';
}

function hideSpinner() {
  var d = document.getElementById('refresh_spinner');
  if (d) {d.style.display = 'none';}
  var d2 = document.getElementById('refresh_spinner_progress');
  if (d2) {d2.style.display = 'none';}
}

function progressSpinner(progress) {
  document.getElementById('refresh_spinner_progress_bar').value = progress;
}

function urlsafeB64Encode(data) {
  return btoa(data).replace(/\+/g, '-').replace(/\//g, '_');
}

function urlsafeB64Decode(data) {
  return atob(data.replace(/-/g, '+').replace(/_/g, '/'));
}

function basicBackendRequest(method, target, parameter, success, failure) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && [200, 201, 204].includes(this.status)) {
      success(this);
      hideSpinner();
    } else if (this.readyState == 4) {
      if (typeof failure !== 'undefined') {
        failure(this);
        hideSpinner();
      } else {
        hideSpinner();
        alertbox(this.status);
      }
    }
  };
  showSpinner();
  var url = 'index.php?Function=' + target;
  if (method == 'POST') {
    xhttp.open(method, url, true);
    if (parameter instanceof FormData) {
      xhttp.setRequestHeader('enctype', 'multipart/form-data');
    } else {
      xhttp.setRequestHeader('Content-type',
        'application/x-www-form-urlencoded');
    }
    xhttp.send(parameter);
  } else {
    xhttp.open('GET', url + '&' + parameter, true);
    xhttp.send();
  }

}


function apiRefresh() {
  var apiAuthorization = localStorage.getItem('ciab_apiAuthorization');
  if (apiAuthorization != null) {
    var json = JSON.parse(apiAuthorization);
    var token = json.refresh_token;
    return apiRequest('POST', 'token',
      'grant_type=refresh_token&refresh_token=' + token + '&client_id=ciab')
      .then(function(result) {
        localStorage.setItem('ciab_apiAuthorization', result.responseText);
      });
  } else {
    return Promise.reject(null);
  }
}


function apiRequest(method, target, inParameter, raw) {
  return new Promise(function(resolve, reject) {
    var parameter = null;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && [200, 201, 204].includes(this.status)) {
        resolve(this);
      } else if (this.readyState == 4) {
        if (this.status == 401) {
          if (!this.responseText) {
            reject(this);
            return;
          }
          var json = JSON.parse(this.responseText);
          if (json.error == 'invalid_token') {
            var refresh = apiRefresh();
            Promise.all([ refresh ]).then(function() {
              apiRequest(method, target, inParameter)
                .then(resolve)
                .catch(reject);
            })
              .catch(reject);
            return;
          }
        }
        reject(this);
      }
    };
    var url = window.location.protocol + '//' + window.location.host + '/api/' +
              target;
    if (method == 'GET') {
      if (inParameter) {
        if (url.indexOf('?') != -1) {
          url += '&' + inParameter;
        } else {
          url += '?' + inParameter;
        }
      }
      xhttp.open(method, url, true);
      parameter = null;
    } else {
      xhttp.open(method, url, true);
      if (inParameter instanceof FormData) {
        xhttp.setRequestHeader('enctype', 'multipart/form-data');
      } else {
        xhttp.setRequestHeader('Content-type',
          'application/x-www-form-urlencoded');
      }
      parameter = inParameter;
    }
    var apiAuthorization = localStorage.getItem('ciab_apiAuthorization');
    if (apiAuthorization != null) {
      var json = JSON.parse(apiAuthorization);
      xhttp.setRequestHeader('Authorization',
        json.token_type + ' ' + json.access_token);
    }
    if (raw) {
      xhttp.responseType = 'blob';
    }
    xhttp.send(parameter);
  }
  );
}
