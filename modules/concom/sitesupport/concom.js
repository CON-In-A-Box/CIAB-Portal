/*
 * Javacript for the ConCom page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar, hideSidebar */

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
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
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
  document.getElementById(division).classList.add('w3-red');

}

function dragLeaveDivision(division) {
  event.preventDefault();
  document.getElementById(division).classList.remove('w3-red');

}

function dropOn(target, source) {
  var tid = document.getElementById(target).getAttribute('data-dbId');
  document.getElementById(target).classList.remove('w3-red');
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

function updateDeptSection(division, pid) {
  if (division) {
    document.getElementById('dept_slider').checked = true;
    document.getElementById('sub_dept').classList.remove('w3-hide');
    if (!document.getElementById('parent').classList.contains('w3-hide')) {
      document.getElementById('parent').classList.add('w3-hide');
    }
  } else {
    if (!document.getElementById('sub_dept').classList.contains('w3-hide')) {
      document.getElementById('sub_dept').classList.add('w3-hide');
    }
    document.getElementById('dept_slider').checked = false;
    document.getElementById('parent').classList.remove('w3-hide');
  }

}

function toggleDept() {
  var slider = document.getElementById('dept_slider');
  var dpbox = document.getElementById('dept_parent');
  var id = document.getElementById('dept_id').value;
  var op = dpbox.getElementsByTagName('option');
  var pid = dpbox.value;
  var i;
  if (!slider.checked && pid == id) {
    dpbox.selectedIndex = 0;
  }
  updateDeptSection(slider.checked, slider.value);

}

function processPosition() {
  confirmbox.close();

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
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('modify=' + btoa(param));

}

function savePosition() {
  confirmbox.start(
      'Confirms Position Details',
      'Are the position details correct?',
      processPosition
  );

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
  dblClick(isDiv, name, -1, division, 0, 0, null);
  document.getElementById('delete_btn').disabled = true;
  showSidebar('edit_position');

}

function deletePosition() {
  confirmbox.start(
      'Confirms Position Deletion',
      'Really delete this position?',
      processDeletion
  );

}

function processDeletion() {
  confirmbox.close();
  var id = document.getElementById('dept_id').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        hideSidebar();
        window.location = 'index.php?Function=concom/admin';
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
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
  input = JSON.parse(atob(json));
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

  updateDeptSection(input.Division, input.Pid);
  var op =
    document.getElementById('dept_parent').getElementsByTagName('option');
  if (input.Division) {
    for (i = 0; i < op.length; i++) {
      if (op[i].value == pid) {
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
    document.getElementById('dept_slider_parent').classList.add('w3-gray');
    document.getElementById('delete_btn').disabled = true;
  } else {
    document.getElementById('dept_slider').disabled = false;
    document.getElementById('dept_slider_parent').classList.remove('w3-gray');
    if (input.Count !== 0) {
      document.getElementById('delete_btn').disabled = true;
    } else {
      document.getElementById('delete_btn').disabled = false;
    }
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
  confirmbox.start(
      'Confirms Email Deletion',
      'Really delete the e-mail address "' + email + '"?',
      processDeleteEmail
  );
}

function processDeleteEmail() {
  confirmbox.close();

  var id = document.getElementById('email_index').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        hideSidebar();
        window.location = 'index.php?Function=concom/admin';
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('deleteEmail=' + id);
}

var _saveEmail = null;

function saveEmail(index) {
  var email = document.getElementById('email_email').value;
  confirmbox.start(
      'Confirms Save Email',
      'Really save the e-mail address "' + email + '"?',
      processSaveEmail
  );
}

function processSaveEmail() {
  confirmbox.close();

  var data = {
      'Id': document.getElementById('email_index').value,
      'Alias': document.getElementById('email_alias').value,
      'Email': document.getElementById('email_email').value,
      'Dept': document.getElementById('email_dept').value,
    };
  if (document.getElementById('email_alias').value === null ||
      document.getElementById('email_alias').value === '' ||
      typeof document.getElementById('email_alias').value === undefined) {
    data.Alias = 'NULL';
  }

  var param = JSON.stringify(data);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        hideSidebar();
        window.location = 'index.php?Function=concom/admin';
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=concom/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('email=' + btoa(param));

}
