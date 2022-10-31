/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, confirmbox, basicBackendRequest, showSpinner,
           hideSpinner, alertbox */
/* exported downloadLog, addField, removeAdmin, updateMemberships,
            doSUDO, rebuildSCSS, setPassword, getLog, randomPass */

function downloadLog() {
  window.location = 'index.php?Function=admin&downloadLog=db';
}

function basicAdminRequest(parameter, finish) {
  basicBackendRequest('POST', 'admin', parameter, finish);
}

function addField() {
  var field = document.getElementById('new_Field').value;
  var value = document.getElementById('new_Value').value;
  value = btoa(value);
  basicAdminRequest('&newField=' + field + '&value=' + value, function() {
    location.reload();
  });
}

function removeAdmin() {
  basicAdminRequest('&removeAdmin=true', function() {
    location.reload();
  });
}

function updateMemberships() {
  confirmbox('Update Memberships?', 'This operations will run in the ' +
    'background and can take several minutes to complete.' +
    '<br><em>Run Command?</em>').then(function() {
    basicAdminRequest('&updateMemberships=true', function() {});
  });
}

function doSUDO() {
  var value = document.getElementById('sudo_id').value;
  apiRequest('POST', 'admin/SUDO/' + value, '');
}

function rebuildSCSS() {
  basicAdminRequest('&rebuildSCSS=true', function() {
    location.reload();
  });
}

function setPassword() {
  var newPassword = document.getElementById('tmp_passwd').value;
  var account = document.getElementById('tmp_login').value;

  showSpinner();
  apiRequest('PUT', 'member/' + account + '/password' ,
    'NewPassword=' + newPassword)
    .then(function() {
      hideSpinner();
      alertbox('Password Updated');
    })
    .catch(function(response) {
      hideSpinner();
      alertbox('Password Reset Failed: ' + response.responseText);
    });
}

function addLogRow(data) {
  var e = document.createElement('DIV');
  e.classList.add('ADMIN-log-row');
  var d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-date');
  d.innerHTML = data.date;
  e.append(d);
  d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-id');
  d.innerHTML = data.account;
  e.append(d);
  d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-function');
  d.innerHTML = data.function;
  e.append(d);
  d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-query');
  var t = data.query.trim();
  if (t.length > 100) {
    var p = document.createElement('P');
    p.classList.add('UI-tooltip');
    p.innerHTML = t.slice(0,100) + ' \u{2026}';
    var s = document.createElement('SPAN');
    s.classList.add('UI-tooltip-text');
    s.innerHTML = t;
    p.append(s);
    d.append(p);
  } else {
    d.innerHTML = t;
  }
  e.append(d);

  document.getElementById('LogEntries').append(e);

}

function getLog(size) {
  document.getElementById('LogEntries').innerHTML = '';
  if (!document.getElementById('db_log').classList.contains('UI-show')) {
    return;
  }
  showSpinner();
  apiRequest('GET', 'admin/log/' + size, null)
    .then(function(response) {
      var d = JSON.parse(response.responseText);
      d.data.forEach(addLogRow);
      hideSpinner();
    })
    .catch(function(response) {
      if (response instanceof Error) { throw response; }
      hideSpinner();
    });
}

function randomPass()
{
  var pw = '';
  var c  = 'bcdfghjklmnprstvwz'; // consonants except hard to speak ones
  var v  = 'aeiou';              // vowels
  var a  = c + v;                // all

  //use three syllables...
  for (var i = 0; i < 3; i++) {
    var sy = c[Math.floor(Math.random() * c.length)];
    sy += v[Math.floor(Math.random() * v.length)];
    sy += a[Math.floor(Math.random() * a.length)];
    if (Math.round(Math.random()) == 0) {
      sy = sy.charAt(0).toUpperCase() + sy.slice(1)
    }
    pw += sy;
  }
  //... and add a nice number
  pw += Math.floor(Math.random() * 89) + 10;

  return pw;
}

function load()
{
  document.getElementById('tmp_passwd').value = randomPass();
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
