/*
 * Base functions for the registration module
 */

/* jshint browser: true */
/* jshint -W097 */
/* global confirmbox, showSpinner, showSidebar, alertbox */
/* exported sidebarMainDiv, refreshBadgeData, printBadge, showUpdateBadge,
            updateBadge, workstationChange */

'use strict';

var sidebarMainDiv = 'main_content';

function refreshBadgeData(badge) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      alertbox('404!');
    }
    else if (this.status == 409) {
      alertbox('409!');
    }
  };
  showSpinner();
  xhttp.open('POST', 'index.php?Function=registration', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('refreshData=' + badge);
}

var _badgeData = null;

function printBadge(data) {
  var input = JSON.parse(atob(data));
  _badgeData = data;
  var msg;
  if (input['Print Count'] > 0) {
    msg =  'Reprint badge for \'';
  } else {
    msg =  'Print badge for \'';
  }
  confirmbox('Confirm Print Badge',
    msg + input['Badge Name'] + '\' ?').then(function() {
    window.location = 'index.php?Function=registration&printBadge=' +
                      _badgeData;
  });
}

function showUpdateBadge(data) {
  var input = JSON.parse(atob(data));
  _badgeData = data;
  showSidebar('update_badge_div');
  document.getElementById('badge_name').value = input['Badge Name'];
}

function updateBadge() {

  var data = JSON.parse(atob(_badgeData));
  data['Badge Name'] = document.getElementById('badge_name').value;
  var param = btoa(JSON.stringify(data));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      alertbox('404!');
    }
    else if (this.status == 409) {
      alertbox('409!');
    }
  };
  showSpinner();
  xhttp.open('POST', 'index.php?Function=registration', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('updateBadge=' + param);
}

function workstationChange() {
  var value = document.getElementById('workstation_id').value;
  var d = new Date();
  var exdays = 5;
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = ';expires=' + d.toUTCString();
  document.cookie = 'CIAB_REGISTRATIONWORKSTATION=' + value + expires +
                    ';path=/';
  var element = document.getElementById('kiosk_mode');
  var disabled = element.classList.contains('UI-disabled');
  var state = (value === '');
  if (state !== disabled) {
    if (state && !disabled)
    {element.classList.add('UI-disabled');}
    if (disabled && !state)
    {element.classList.remove('UI-disabled');}
  }
}
