/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, confirmbox, basicBackendRequest, showSpinner,
           hideSpinner, alertbox */
/* exported downloadLog, addField, removeAdmin, updateMemberships,
            doSUDO, rebuildSCSS, setPassword, getLog */

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
  d.innerHTML = data.Date;
  e.append(d);
  d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-id');
  d.innerHTML = data.AccountID;
  e.append(d);
  d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-function');
  d.innerHTML = data.Function;
  e.append(d);
  d = document.createElement('DIV');
  d.classList.add('ADMIN-log-col-query');
  var t = data.Query.trim();
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
