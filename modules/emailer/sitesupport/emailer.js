/*
 * Javacript for the Emailer
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar, showSpinner, hideSidebar, hideSpinner,
           buildPositionList, buildDepartmentList, lists, quill,
           urlsafeB64Encode */
/* exported removeAccess, changePosition, addAccess, changeDepartment,
            backFromAccess, toChanged, testList, newList, updateList,
            backFromEmail, editEmail, sendEmail, sidebarMainDiv,
            cancelEmail, accessList */

'use strict';

var sidebarMainDiv = 'main_content';

function doCancelEmail() {
  confirmbox.close();
  document.getElementById('email_subject').value = '';
  document.getElementById('email_to').value = 'None';
  document.getElementById('to_count').innerHTML = '0';
  quill.setText('');
}

function cancelEmail() {
  confirmbox.start('Confirm Clear Email',
    'Cancel Email and clear all fields?', doCancelEmail);
}

function doSendEmail() {
  confirmbox.close();

  var data = {
    'from' : document.getElementById('email_from').value,
    'reply' : document.getElementById('email_reply').value,
    'subject' : document.getElementById('email_subject').value,
    'to' : document.getElementById('email_to').value,
    'body' : quill.container.firstChild.innerHTML
  };
  var param = urlsafeB64Encode(JSON.stringify(data));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSpinner();
      window.alert('Email Sent!');
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=emailer', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.setRequestHeader('enctype', 'multipart/form-data');
  showSpinner();
  xhttp.send('send=' + param);
}

function sendEmail() {
  if (document.getElementById('email_to').value == 'None')
  {
    window.alert('No \'To\' Addresses selected.');
    return;
  }

  if (document.getElementById('email_subject').value == '')
  {
    window.alert('Email Subject Required.');
    return;
  }

  if (quill.getLength() < 2)
  {
    window.alert('Email Body Required.');
    return;
  }

  confirmbox.start('Confirm Send Email',
    'Send Email to all recipients?', doSendEmail);
}

var accessListData = null;

function editEmail(data) {
  var json = JSON.parse(atob(data));
  document.getElementById('email_id').value = json.EmailListID;
  document.getElementById('email_name').value = json.DBName;
  document.getElementById('email_description').value = json.DBDescription;
  document.getElementById('email_code').value = json.Code;
  if (json.ChangeAccess != '0') {
    document.getElementById('access_list').classList.remove('UI-hide');
  } else {
    document.getElementById('access_list').classList.add('UI-hide');
  }
  hideSidebar();
  accessListData = null;
  showSidebar('edit_email');
}

function backFromEmail() {
  hideSidebar();
  addList();
}

function doUpdateList() {
  var data = {
    'id' : document.getElementById('email_id').value,
    'name' : document.getElementById('email_name').value,
    'description' : document.getElementById('email_description').value,
    'code': document.getElementById('email_code').value,
    'access': null,
  };
  if (accessListData != null) {
    data.access = Array();
    accessListData.forEach(function(entry) {
      data.access.push(Object.assign({}, entry));
    });
  }
  var param = btoa(JSON.stringify(data));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSidebar();
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=emailer', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('updateList=' + param);
}

function updateList() {
  if (quill.getLength() > 1)
  {
    confirmbox.start('Draft will be lost',
      'Updating this will result in the loss of your draft!<br>Continue?',
      doUpdateList);
  } else {
    doUpdateList();
  }
}

function addList() {
  var i = 0;
  var div = document.getElementById('email_lists');
  div.innerHTML = '';
  for (i = 0; i < lists.length; i++) {
    var data = JSON.parse(atob(lists[i]));
    div.innerHTML += '<button onclick=\'editEmail("' + lists[i] + '");\'' +
                     ' class="UI-roundbutton">' + data.DBName +
                     '</button>';
    div.innerHTML += '<br>\n';
  }
  showSidebar('update_lists_div');
}

function newList() {
  hideSidebar();
  document.getElementById('email_id').value = -1;
  document.getElementById('email_name').value = '';
  document.getElementById('email_description').value = '';
  document.getElementById('email_code').value = '';
  document.getElementById('access_list').classList.remove('UI-hide');
  accessListData = null;
  showSidebar('edit_email');
}

function testList() {
  var v = document.getElementById('email_code').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var d = this.responseText;
      window.alert(d + ' addresses');
    }
  };
  xhttp.open('GET', 'index.php?Function=emailer&test=' + btoa(v), true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send();
}

function toChanged() {
  var v = document.getElementById('email_to').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var d = this.responseText;
      document.getElementById('to_count').innerHTML = d;
    }
  };
  xhttp.open('GET', 'index.php?Function=emailer&listCount=' + v, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send();
}

function backFromAccess() {
  updateAccess();
  hideSidebar();
  showSidebar('edit_email');
}

function updateAccess() {
  if (accessListData == null) {return;}
  accessListData.forEach(function(value, index) {
    var e = document.getElementById('position_' + index);
    if (e.value != value.PositionID) {
      value.PositionID = e.value;
      value.Position = e.options[e.selectedIndex].innerHTML;
    }
    e = document.getElementById('department_' + index);
    if (e.value != value.DepartementID) {
      value.DepartmentID = e.value;
      value.Department = e.options[e.selectedIndex].innerHTML;
    }

    e = document.getElementById('check1_' + index);
    if (e) {
      if (e.checked)
      {value.EditList = '1';}
      else
      {value.EditList = '0';}
    }
    e = document.getElementById('check2_' + index);
    if (e) {
      if (e.checked)
      {value.ChangeAccess = '1';}
      else
      {value.ChangeAccess = '0';}
    }
  });
}

function checkAccessDup() {
  for (var i = 0; i < accessListData.length; i++) {
    var d = document.getElementById('department_' + i);
    var p = document.getElementById('position_' + i);
    var ok = true;
    accessListData.forEach(function(value, index) {
      if (index == i) {return;}
      if (ok && value.DepartmentID == d.value && value.PositionID == p.value) {
        window.alert('Duplicate Access');
        ok = false;
      }
    });
    if (!ok) {
      return false;
    }
  }
  return true;

}

function changeDepartment(idx)
{
  if (!checkAccessDup()) {
    document.getElementById('department_' + idx).value =
        accessListData[idx].DepartmentID;
  } else {
    updateAccess();
  }
}

function changePosition(idx)
{
  if (!checkAccessDup()) {
    document.getElementById('position_' + idx).value =
        accessListData[idx].PositionID;
  } else {
    updateAccess();
  }
}

function addAccess() {
  var data = [];
  var d = document.getElementById('department_new');
  var p = document.getElementById('position_new');
  var ok = true;

  if (accessListData == null) {
    accessListData = Array();
  }
  updateAccess();
  accessListData.forEach(function(value) {
    if (ok && value.DepartmentID == d.value && value.PositionID == p.value) {
      window.alert('Duplicate Access');
      ok = false;
      return;
    }
  });

  if (!ok) {
    return;
  }

  data.Department = d.options[d.selectedIndex].innerHTML;
  data.Position = p.options[p.selectedIndex].innerHTML;
  data.DepartmentID = d.value;
  data.PositionID = p.value;
  data.EmailListID = document.getElementById('email_id').value;
  if (document.getElementById('check1_new').checked)
  {data.EditList = '1';}
  else
  {data.EditList = '0';}
  if (document.getElementById('check2_new').checked)
  {data.ChangeAccess = '1';}
  else
  {data.ChangeAccess = '0';}

  accessListData.push(data);
  buildAccessList(accessListData);
}

function removeAccess(idx) {
  updateAccess();
  accessListData.splice(idx, 1);
  buildAccessList(accessListData);
}

function buildAccessList(list) {
  var output = '<div class=\'UI-table\'>\n';
  output += '<div class=\'UI-table-header\'>\n';
  output += '<div class=\'UI-table-row\'>\n';
  var skip = 2;
  for (var propertyName in list[0]) {
    if (propertyName == 'EmailListID') {continue;}
    output += '<div class=\'';
    if (propertyName.includes('ID')) {
      output += 'UI-table-hidden';
    } else {
      output += 'UI-table-cell';
    }
    output += '\'>';
    output += '<div ';
    if (skip <= 0) {
      output += 'class=\'UI-table-vertical\'';
    }
    skip -= 1;
    var cleanPropertyName = propertyName.replace(/([A-Z])/g, '&nbsp;$1').trim();
    output += '>' + cleanPropertyName + '</div>';
    output += '</div>\n';
  }
  output += '<div class=\'UI-table-cell\'>' +
            '<div class=\'UI-table-vertical\'>&nbsp;</div></div>';
  output += '</div>\n';
  output += '</div>\n';
  var dept = Array();
  var position = Array();
  var idx = 0;
  list.forEach(function(element) {
    if (element.Department == null) {
      return;
    }
    output += '<div class=\'UI-table-row\'>\n';
    var check = 1;
    for (var propertyName in element) {
      var hide = false;
      if (propertyName == 'EmailListID') {continue;}
      output += '<div class=\'';
      if (propertyName.includes('ID')) {
        output += 'UI-table-hidden';
        hide = true;
      } else {
        output += 'UI-table-cell';
      }
      output += '\'>';
      if (propertyName == 'Department') {
        output += buildDepartmentList(idx);
        dept[idx] = element.DepartmentID;
      } else if (propertyName == 'Position') {
        output += buildPositionList(idx);
        position[idx] = element.PositionID;
      } else if (!hide) {
        var checked = !!+element[propertyName];
        output += '<input class="EMAILER-check" id="check' + check;
        output += '_' + idx + '" type="checkbox" ';
        if (checked) {
          output += 'checked="checked"';
        }
        output += '>';
        check += 1;
      }
      output += '</div>\n';
    }
    output += '<div class=UI-table-cell><button onclick=\'removeAccess(' +
              idx + ')\' class=\'UI-roundbutton\'>' +
              '<i class=\'fa fa-minus-square\'></i></button></div>';
    output += '</div>\n';
    idx += 1;
  });
  // Blank One
  output += '<div class=\'UI-table-row UI-red\'>\n';
  output += '<div class=UI-table-cell>';
  output += buildDepartmentList('new');
  output += '</div>';
  output += '<div class=UI-table-cell>';
  output += buildPositionList('new');
  output += '</div>';
  output += '<div class=UI-table-cell>';
  output += '<input class="EMAILER-check" id="check1_new" type="checkbox">';
  output += '</div>';
  output += '<div class=UI-table-cell>';
  output += '<input class="EMAILER-check" id="check2_new" type="checkbox">';
  output += '</div>';
  output += '<div class=UI-table-cell>' +
            '<button class=\'UI-roundbutton\' onclick=\'addAccess()\'>' +
            '<i class=\'fa fa-plus-square\'></i></button></div>';
  output += '</div>\n';
  document.getElementById('access_list_data').innerHTML = output;
  dept.forEach(function(value, index) {
    document.getElementById('department_' + index).value = value;
  });
  position.forEach(function(value, index) {
    document.getElementById('position_' + index).value = value;
  });
}

function accessList() {
  hideSidebar();
  if (accessListData != null) {
    buildAccessList(accessListData);
    showSidebar('edit_access');
    return;
  }
  var n = document.getElementById('email_name').value;
  document.getElementById('edit_name').innerHTML = n;
  var v = document.getElementById('email_id').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var data = JSON.parse(this.responseText);
      buildAccessList(data);
      if (data.length && data[0].Department !== null) {
        accessListData = data;
      }
      showSidebar('edit_access');
    }
  };
  xhttp.open('GET', 'index.php?Function=emailer&accessControl=' + v, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send();
}
