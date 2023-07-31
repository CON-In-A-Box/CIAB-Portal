/*
 * Javacript for the ConCom page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar, hideSidebar,
           PERMISSIONS, basicBackendRequest*/
/* exported drag, dragOverDivision, dragLeaveDivision, dragDropDivision,
            dragDropParent, toggleDept, savePosition, newEntry,
            deletePosition, changeEmail, editEmail, returnPosition,
            deleteEmail, saveEmail, deleteAC, newAC, savePermission,
            returnRBAC, onAdd, addMember,
            updateMember, deleteMember, editMember */

var basicReload = function() {
  window.location = 'index.php?Function=concom/admin';
};

function basicConcomRequestAdmin(method, parameter, finish) {
  basicBackendRequest(method, 'concom/admin', parameter, finish);

}

function setParent(id, newParent) {
  var data = {
    'Id': id,
    'newParent': newParent
  };
  var param = btoa(JSON.stringify(data));

  basicConcomRequestAdmin('POST', 'reparent=' + param, basicReload);

}

function clearSelection() {
  if (window.getSelection) {
    window.getSelection().removeAllRanges();
  } else if (document.selection) {
    document.selection.empty();
  }

}

function drag(ev) {
  ev.dataTransfer.setData('text', ev.target.id);
  clearSelection();

}

function dragOverDivision(division) {
  event.preventDefault();
  document.getElementById(division).classList.add('UI-red');

}

function dragLeaveDivision(division) {
  event.preventDefault();
  document.getElementById(division).classList.remove('UI-red');

}

function dropOn(target, source) {
  var tid = document.getElementById(target).getAttribute('data-dbId');
  document.getElementById(target).classList.remove('UI-red');
  setParent(source, tid);

}

function dragDropDivision(ev) {
  var data = ev.dataTransfer.getData('text');
  dropOn(ev.target.id, data);

}

function dragDropParent(ev, depth) {
  ev.stopPropagation();
  var data = ev.dataTransfer.getData('text');
  dropOn(ev.path[depth].id, data);

}

function updateDeptSection(division) {
  if (division) {
    document.getElementById('dept_slider').checked = true;
    document.getElementById('sub_dept').classList.remove('UI-hide');
    if (!document.getElementById('parent').classList.contains('UI-hide')) {
      document.getElementById('parent').classList.add('UI-hide');
    }
    document.getElementById('fallback_dept').classList.remove('UI-hide');
  } else {
    if (!document.getElementById('sub_dept').classList.contains('UI-hide')) {
      document.getElementById('sub_dept').classList.add('UI-hide');
    }
    if (!document.getElementById('fallback_dept').classList.contains(
      'UI-hide')) {
      document.getElementById('fallback_dept').classList.add('UI-hide');
    }
    document.getElementById('dept_slider').checked = false;
    document.getElementById('parent').classList.remove('UI-hide');
  }

}

function toggleDept() {
  var slider = document.getElementById('dept_slider');
  var dpbox = document.getElementById('dept_parent');
  var id = document.getElementById('dept_id').value;
  var pid = dpbox.value;
  if (!slider.checked && pid == id) {
    dpbox.selectedIndex = 0;
  }
  updateDeptSection(slider.checked);

}

function savePosition() {
  confirmbox(
    'Confirms Position Details',
    'Are the position details correct?').then(function() {
    var data = {
      'Id': document.getElementById('dept_id').value,
      'Name': document.getElementById('dept_name').value,
      'ParentDept': document.getElementById('dept_parent').value,
      'FallbackID': document.getElementById('dept_fallback').value,
    };

    var isDiv = document.getElementById('dept_slider').checked;
    if (isDiv && data.Id != data.ParentDept) {
      data.ParentDept = data.Id;
    }

    var param = btoa(JSON.stringify(data));
    basicConcomRequestAdmin('POST', 'modify=' + param, basicReload);
  });
}

function newEntry(division) {
  clearSelection();
  var isDiv = (division == -1);
  var name;
  if (isDiv) {
    name = 'New Division';
  } else {
    name = 'New Department';
  }

  var data = {
    'Division': isDiv,
    'Name': name,
    'Id': -1,
    'Pid': division,
    'Count' : 0,
    'Children': 0,
    'Email': null
  };

  dblClick(btoa(JSON.stringify(data)));
  document.getElementById('delete_btn').disabled = true;
  document.getElementById('dept_rbac').disabled = true;
  document.getElementById('dept_rbac').title = 'Edit RBAC after creation';

  showSidebar('edit_position');
  if (isDiv) {
    getFallbackOptions(-1, -1);
  }

}

function deletePosition() {
  confirmbox(
    'Confirms Position Deletion',
    'Really delete this position?').then(processDeletion);

}

function processDeletion() {
  var id = document.getElementById('dept_id').value;

  basicConcomRequestAdmin('POST', 'delete=' + id, basicReload);

}

var _currentSection = null;

function dblClick(json) {
  clearSelection();
  _currentSection = json;
  var input = JSON.parse(atob(json));
  document.getElementById('dept_id').value = input.Id;
  document.getElementById('dept_name').value = input.Name;
  document.getElementById('dept_parent').value = input.Pid;
  document.getElementById('dept_count').value = input.Count;
  document.getElementById('dept_sub').value = input.Children;

  var i;
  var div = document.getElementById('dept_email');
  div.innerHTML = '';
  if (input.Email !== null) {
    for (i = 0; i < input.Email.length; i++) {
      var data = input.Email[i];
      div.innerHTML += '<button onclick=\'editEmail("' + json + '", ' +
                        i + ', ' + input.Id + ');\'' +
                       ' class="UI-roundbutton">' + data.EMail +
                       '</button>';
      div.innerHTML += '<br>\n';
    }
  }
  div.innerHTML += '<button onclick=\'editEmail(null, -1, ' + input.Id +
                   ');\' ' +
                   'class="UI-roundbutton"> ' +
                   '<i class=\'fas fa-plus-square\'></i></button>\n';

  div = document.getElementById('dept_rbac');
  if (div) {
    div.setAttribute('onclick','showRBAC(' + input.Id + ');');
  }

  updateDeptSection(input.Division);
  var op =
    document.getElementById('dept_parent').getElementsByTagName('option');
  if (input.Division) {
    for (i = 0; i < op.length; i++) {
      if (op[i].value == input.Pid) {
        op[i].disabled = true;
      } else {
        op[i].disabled = false;
      }
    }
  } else {
    for (i = 0; i < op.length; i++) {
      op[i].disabled = false;
    }
  }
  if (input.Children > 0) {
    document.getElementById('dept_slider').disabled = true;
    document.getElementById('dept_slider_parent').classList.add('UI-gray');
    document.getElementById('delete_btn').disabled = true;
  } else {
    document.getElementById('dept_slider').disabled = false;
    document.getElementById('dept_slider_parent').classList.remove('UI-gray');
    document.getElementById('delete_btn').disabled = false;
  }
  document.getElementById('dept_rbac').disabled = false;
  document.getElementById('dept_rbac').title = '';
  showSidebar('edit_position');
  if (input.Id == input.Pid) {
    getFallbackOptions(input.Id, input.Fallback);
  }
}

function changeEmail() {
  if (document.getElementById('email_email').value.length > 0 &&
     (document.getElementById('email_original').value !=
      document.getElementById('email_email').value)) {
    document.getElementById('email_save_btn').disabled = false;
  } else {
    document.getElementById('email_save_btn').disabled = true;
  }
}

function  editEmail(data, index, deptId) {
  document.getElementById('email_dept').value = deptId;
  if (data !== null) {
    var email = JSON.parse(atob(data));
    document.getElementById('email_original').value = email.Email[index].EMail;
    document.getElementById('email_email').value = email.Email[index].EMail;
    document.getElementById('email_alias').value = email.Email[index].IsAlias;
    document.getElementById('email_index').value =
        email.Email[index].EMailAliasID;
    document.getElementById('email_delete_btn').disabled = false;
  } else {
    document.getElementById('email_email').value = '';
    document.getElementById('email_delete_btn').disabled = true;
    document.getElementById('email_alias').value = null;
    document.getElementById('email_index').value = -1;
  }
  document.getElementById('email_save_btn').disabled = true;
  hideSidebar();
  showSidebar('edit_email');
}

function returnPosition() {
  if (_currentSection == null) {
    hideSidebar();
  } else {
    dblClick(_currentSection);
  }
}

function deleteEmail() {
  var email = document.getElementById('email_original').value;
  confirmbox(
    'Confirms Email Deletion',
    'Really delete the e-mail address "' + email + '"?').then(
    processDeleteEmail
  );
}

function processDeleteEmail() {
  var id = document.getElementById('email_index').value;
  basicConcomRequestAdmin('POST', 'deleteEmail=' + id, basicReload);

}

function saveEmail() {
  var email = document.getElementById('email_email').value;
  confirmbox(
    'Confirms Save Email',
    'Really save the e-mail address "' + email + '"?').then(processSaveEmail);
}

function processSaveEmail() {
  var data = {
    'Id': document.getElementById('email_index').value,
    'Alias': document.getElementById('email_alias').value,
    'Email': document.getElementById('email_email').value,
    'Dept': document.getElementById('email_dept').value,
  };
  if (document.getElementById('email_alias').value === null ||
      document.getElementById('email_alias').value === '' ||
      typeof document.getElementById('email_alias').value == undefined) {
    data.Alias = 'NULL';
  }

  var param = btoa(JSON.stringify(data));
  basicConcomRequestAdmin('POST', 'email=' + param, basicReload);

}

function editRBAC(dep, input) {
  var permissions = JSON.parse(input);
  var block = '';
  var inh;
  var data;
  var key;

  if (dep == 'all') {
    document.getElementById('rbac_title').innerHTML = 'All Staff Permissions';
  } else {
    document.getElementById('rbac_title').innerHTML = 'Position Permissions';
  }

  for (key in permissions) {
    data = permissions[key];
    block += '<span><b>' + data.name + '</b>:  ';
    for (inh in data.inherited) {
      block += '<span>' + data.inherited[inh] + '</span>  ';
    }
    block += '</span><br>';
  }
  var div = document.getElementById('inherited');
  div.innerHTML = block;

  block = '';
  for (key in permissions) {
    data = permissions[key];
    block += '<span><b>' + data.name + '</b>:  ';
    for (inh in data.position) {
      block += '<button onclick=\'deleteAC("' + dep + '",' + key + ',"' +
                data.position[inh] + '");\'' +
                   ' class="UI-roundbutton">' +
                   '<i class=\'fas fa-minus-square\'></i>&nbsp;' +
                   data.position[inh] +
                   '</button>';
    }
    block += '<button onclick=\'newAC("' + dep + '",' + key + ');\' ' +
               'class="UI-roundbutton"> ' +
               '<i class=\'fas fa-plus-square\'></i></button>\n';
    block += '</span><br>';
  }
  div = document.getElementById('position');
  div.innerHTML = block;
  showSidebar('edit_rbac');
}

function showRBAC(id) {
  if (id == 'all') {
    _currentSection = null;
  }
  basicConcomRequestAdmin('GET', 'permissions=' + id, function(response) {
    editRBAC(id, response.responseText);
  });

}

function deleteAC(dep, pos, perm) {
  confirmbox(
    'Confirms Permission Deletion',
    'Really delete "' + perm + '" permission?').then(function() {
    basicConcomRequestAdmin('POST', 'deleteAC=' + dep + '&position=' + pos +
                              '&permission=' + perm, function() {
      showRBAC(dep);
    });
  });

}

var _dep;

function newAC(department, position) {
  _dep = department;
  document.getElementById('perm_dept').value = department;
  document.getElementById('perm_position').value = position;
  document.getElementById('perm_perm').options.length = 0;
  var compare = function(a,b) {
    return (a.permission > b.permission);
  };
  PERMISSIONS.sort(compare);
  PERMISSIONS.forEach(function(val) {
    var option = document.createElement('option');
    option.text = val.permission;
    option.title = val.description;
    document.getElementById('perm_perm').options.add(option);
  });
  hideSidebar();
  showSidebar('add_ac');
}

function returnRBAC() {
  hideSidebar();
  showRBAC(_dep);
}

function savePermission() {
  var perm =  document.getElementById('perm_perm').value;
  confirmbox(
    'Confirms Permission Addition',
    'Really add permission \'' + perm + '\' ?').then(permissionSave);

}

function permissionSave() {
  var dep = document.getElementById('perm_dept').value;
  var pos = document.getElementById('perm_position').value;
  var perm =  document.getElementById('perm_perm').value;

  basicConcomRequestAdmin('POST', 'addAC=' + dep + '&position=' + pos +
                          '&permission=' + perm, function() {
    showRBAC(dep);
  });

}


function getFallbackOptions(id, fallback) {
  var dropdown =  document.getElementById('dept_fallback');
  dropdown.innerHTML = '';
  var option = document.createElement('option');
  option.text = '----';
  option.value = -1;
  dropdown.add(option);

  basicConcomRequestAdmin('GET', 'fallbackList=' + id, function(response) {
    var data = JSON.parse(response.responseText);
    var i = 0;
    data.forEach(function(element) {
      var option = document.createElement('option');
      option.text = element.Name;
      option.value = element.DepartmentID;
      dropdown.add(option);
      if (element.Name == fallback) {
        i = dropdown.length - 1;
      }
    });
    dropdown.selectedIndex = i;
  });
}

function onAdd() {
  var department = document.getElementById('add_departemnt').innerHTML;
  var parameter = 'AddDepartment=' + department;
  parameter += '&accountId=' + document.getElementById('user_id').value;
  parameter += '&Position=' + document.getElementById('position').value;
  basicBackendRequest('POST', 'concom', parameter, function() {
    window.location.assign('index.php?Function=concom#' + department);
    window.location.reload(true);
  });
}

function addMember(department, posData) {
  var positions = JSON.parse(atob(posData));
  document.getElementById('add_departemnt').innerHTML = department;
  var pos =  document.getElementById('position');
  pos.innerHTML = '';
  positions.forEach(function(d, i) {
    if (d !== null) {
      var option = document.createElement('option');
      option.text = d;
      option.value = i + 1;
      pos.add(option);
    }
  });
  document.getElementById('add_button').disabled = true;
  showSidebar('add_member_div');
}

function updateMember() {
  var pos = document.getElementById('user_pos');
  var userPos = pos.options[pos.options.selectedIndex].value;
  var parameter = 'Modify=' + document.getElementById('user_id').value;
  parameter += '&Department=' + document.getElementById('user_div').value;
  parameter += '&Position=' + userPos;
  parameter += '&Note=' + document.getElementById('user_notes').value;
  basicBackendRequest('POST', 'concom', parameter, function() {
    hideSidebar();
    window.location.reload(true);
  });
}

function deleteMember() {
  var target = document.getElementById('user_id').value;
  var department = document.getElementById('user_div').value;
  var position = document.getElementById('user_oldpos').value;

  confirmbox(
    'Are you sure you want to remove ' +
    document.getElementById('user_name').innerHTML +
    ' from ' +
    document.getElementById('user_div').value).then(function() {
    window.location = 'index.php?Function=concom&Remove=' + encodeURI(target) +
        '&Department=' + encodeURI(department) + '&Position=' +
        encodeURI(position);
    hideSidebar();
  });
}

function editMember(name, id, div, pos, notes, posData) {
  document.getElementById('user_id').value = id;
  document.getElementById('user_div').value = div;
  document.getElementById('user_name').innerHTML = name;
  document.getElementById('user_notes').value = notes;
  document.getElementById('user_desc').innerHTML = pos + ' in ' + div;

  var positions = JSON.parse(atob(posData));
  var poss =  document.getElementById('user_pos');
  poss.innerHTML = '';
  var s = 0;
  positions.forEach(function(d, i) {
    if (d !== null) {
      var option = document.createElement('option');
      option.text = d;
      option.value = i + 1;
      poss.add(option);
      if (d == pos) {
        document.getElementById('user_oldpos').value = option.value;
        s = poss.length - 1;
      }
    }
  });
  poss.selectedIndex = s;

  showSidebar('edit_member_div');
}
