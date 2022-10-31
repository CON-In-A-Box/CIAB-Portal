/*
 * Base functions for the registration module
 */

/* jshint browser: true */
/* jshint -W097 */
/* global confirmbox, showSidebar, basicBackendRequest, showSpinner,
          hideSpinner, apiRequest */
/* exported sidebarMainDiv, refreshBadgeData, printBadge,
            showUpdateBadge, updateBadge, workstationChange
            */

'use strict';

var sidebarMainDiv = 'main_content';

function basicRegistrationRequest(parameter, finish) {
  basicBackendRequest('POST', 'registration', parameter, finish);
}

function refreshBadgeData(badge) {
  basicRegistrationRequest('refreshData=' + badge, function() {
    location.reload();
  });
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
    msg + input['Badge Name'] + '\' ?')
    .then(function() {
      showSpinner();
      apiRequest('PUT', 'registration/ticket/' + data['RegID'] + '/print', null)
        .then(function() {
          hideSpinner();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        });
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
  var name = document.getElementById('badge_name').value;
  showSpinner();
  apiRequest('PUT', 'registration/ticket/' +  data['RegID'],'badge_name=' +
             name)
    .then(function() {
      hideSpinner();
      location.reload();
    })
    .catch(function(response) {
      hideSpinner();
      if (response instanceof Error) { throw response; }
    })
}

function workstationChange() {
  var value = document.getElementById('workstation_id').value;
  var d = new Date();
  var exdays = 5;
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = ';expires=' + d.toUTCString();
  document.cookie = 'CIAB_REGISTRATIONWORKSTATION=' + value + expires +
                    ';path=/';
  var element = document.getElementById('console_mode');
  var disabled = element.classList.contains('UI-disabled');
  var state = (value === '');
  if (state !== disabled) {
    if (state && !disabled)
    {element.classList.add('UI-disabled');}
    if (disabled && !state)
    {element.classList.remove('UI-disabled');}
  }
}
