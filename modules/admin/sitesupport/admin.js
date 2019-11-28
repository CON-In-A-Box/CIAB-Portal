/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, basicBackendRequest */
/* exported downloadLog, setField, addField, removeAdmin, updateMemberships */

function downloadLog() {
  window.location = 'index.php?Function=admin&downloadLog=db';
}

function basicAdminRequest(parameter, finish) {
  basicBackendRequest('POST', 'admin', parameter, finish);
}

function setField(field) {
  var value = document.getElementById('config_' + field).value;
  value = btoa(value);
  basicAdminRequest('&setField=' + field + '&value=' + value, function() {
    location.reload();
  });
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
