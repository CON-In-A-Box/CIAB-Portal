/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* exported  generateDeptReport, sidebarMainDiv */

'use strict';

var sidebarMainDiv = 'info_div';

function generateDeptReport() {
  var name = document.getElementById('dept_data_name').value;
  var deptid = document.getElementById('dept_data').value;
  window.location = 'index.php?Function=volunteers/report&dept_report=' +
                    deptid + '&dept_name=' + name;
}
