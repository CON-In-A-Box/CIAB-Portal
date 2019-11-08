/*
 * Javacript for the ConCom page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar, hideSidebar, showSpinner, hideSpinner,
           PERMISSIONS, alertbox */
/* exported drag, dragOverDivision, dragLeaveDivision, dragDropDivision,
            dragDropParent, toggleDept, savePosition, newEntry,
            deletePosition, changeEmail, editEmail, returnPosition,
            deleteEmail, saveEmail, deleteAC, newAC, savePermission,
            returnRBAC, confirmRemoval */

function setParent(id, newParent) {
  var data = {
    'Id': id,
    'newParent': newParent
  };
  var param = JSON.stringify(data);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.location = 'index.php?Function=concom/admin';
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('reparent=' + btoa(param));

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
  } else {
    if (!document.getElementById('sub_dept').classList.contains('UI-hide')) {
      document.getElementById('sub_dept').classList.add('UI-hide');
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

function processPosition() {
  var data = {
    'Id': document.getElementById('dept_id').value,
    'Name': document.getElementById('dept_name').value,
    'ParentDept': document.getElementById('dept_parent').value,
  };

  var isDiv = document.getElementById('dept_slider').checked;
  if (isDiv && data.Id != data.ParentDept) {
    data.ParentDept = data.Id;
  }

  var param = JSON.stringify(data);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSidebar();
      window.location = 'index.php?Function=concom/admin';
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('modify=' + btoa(param));

}

function savePosition() {
  confirmbox(
    'Confirms Position Details',
    'Are the position details correct?').then(processPosition);

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
  showSidebar('edit_position');

}

function deletePosition() {
  confirmbox(
    'Confirms Position Deletion',
    'Really delete this position?').then(processDeletion);

}

function processDeletion() {
  var id = document.getElementById('dept_id').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSidebar();
      window.location = 'index.php?Function=concom/admin';
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('delete=' + id);

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
                   '<i class=\'fa fa-plus-square\'></i></button>\n';

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
  showSidebar('edit_position');
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
  dblClick(_currentSection);
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
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSidebar();
      window.location = 'index.php?Function=concom/admin';
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('deleteEmail=' + id);
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

  var param = JSON.stringify(data);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSidebar();
      window.location = 'index.php?Function=concom/admin';
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('email=' + btoa(param));

}

function editRBAC(dep, input) {
  var permissions = JSON.parse(input);
  var block = '';
  var inh;
  var data;
  var key;
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
      block += '<button onclick=\'deleteAC(' + dep + ',' + key + ',"' +
                data.position[inh] + '");\'' +
                   ' class="UI-roundbutton">' +
                   '<i class=\'fa fa-minus-square\'></i>&nbsp;' +
                   data.position[inh] +
                   '</button>';
    }
    block += '<button onclick=\'newAC(' + dep + ',' + key + ');\' ' +
               'class="UI-roundbutton"> ' +
               '<i class=\'fa fa-plus-square\'></i></button>\n';
    block += '</span><br>';
  }
  div = document.getElementById('position');
  div.innerHTML = block;
  showSidebar('edit_rbac');
}

function showRBAC(id) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSpinner();
      editRBAC(id, this.responseText);
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  xhttp.open('GET', 'index.php?Function=concom/admin&permissions=' + id, true);
  showSpinner();
  xhttp.send();
}

var _dep;
var _pos;
var _perm;

function deleteAC(dep, pos, perm) {
  _dep = dep;
  _pos = pos;
  _perm = perm;
  confirmbox(
    'Confirms Permission Deletion',
    'Really delete "' + perm + '" permission?').then(permissionDeletion);

}

function permissionDeletion() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSpinner();
      showRBAC(_dep);
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  showSpinner();
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('deleteAC=' + _dep + '&position=' + _pos + '&permission=' + _perm);
}

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

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSpinner();
      showRBAC(dep);
    } else if (this.status == 404) {
      alertbox('404!');
    } else if (this.status == 409) {
      alertbox('409!');
    }
  };
  showSpinner();
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('addAC=' + dep + '&position=' + pos + '&permission=' + perm);
}

function confirmRemoval(fname, lname, target, department, position) {
  confirmbox(
    'Are you sure you want to remove ' + fname + '&nbsp;' + lname +
    ' from ' + position + ' in ' + department).then(function() {
    window.location = 'index.php?Function=concom&Remove=' + encodeURI(target) +
        '&Department=' + encodeURI(department) + '&Position=' +
        encodeURI(position);
  });

}
