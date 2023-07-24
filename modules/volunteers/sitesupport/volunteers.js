/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals userEmail, checkAuthentication, adminMode, alertbox, basicBackendRequest */
/* exported  generateDeptReport, toggleAdminMode, sidebarMainDiv , basicVolunteersRequestAdmin*/

'use strict';

var sidebarMainDiv = 'info_div';

function basicVolunteersRequestAdmin(parameter, finish) {
  basicBackendRequest('POST', 'volunteers/admin', parameter, finish);
}

function enterAdmin() {
  setTimeout(location.reload(), 1000);
}

function failAdmin(error) {
  document.getElementById('admin_slider').checked = false;
  if (error) {
    alertbox('Login Failed (' + error + ')');
  }
}

function toggleAdminMode() {
  document.cookie = 'CIAB_VOLUNTEERADMIN=;expires=Thu, 01 Jan 1970 ' +
                    '00:00:01 GMT;';
  var target = '';
  var userId = null;
  const searchParams = new URLSearchParams(window.location.search);
  if (searchParams.has('volunteerId')) {
    userId = searchParams.get('volunteerId');
  }
  if (userId) {
    target = 'index.php?Function=volunteers/admin&volunteerId=' + userId;
  } else {
    target = 'index.php?Function=volunteers/admin';
  }
  if (!adminMode) {
    checkAuthentication(userEmail, enterAdmin, failAdmin,
      {target: 'volunteers/admin'});
  } else {
    setTimeout(function() {window.location = target;}, 1000);
  }
}


function generateDeptReport() {
  var name = document.getElementById('dept_data_name').value;
  var deptid = document.getElementById('dept_data').value;
  window.location = 'index.php?Function=volunteers/report&dept_report=' +
                    deptid + '&dept_name=' + name;
}
