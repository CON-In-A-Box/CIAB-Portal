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
        window.location = 'index.php?Function=concom&admin=1';
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=concom', true);
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

function dblClick(division, name, id, pid, childCount, count) {
  clearSelection();
  document.getElementById('dept_id').value = id;
  document.getElementById('dept_name').value = name;
  document.getElementById('dept_parent').value = pid;
  document.getElementById('dept_count').value = count;
  document.getElementById('dept_sub').value = childCount;
  updateDeptSection(division, pid);
  var op = document.getElementById('dept_parent').getElementsByTagName('option');
  if (division) {
    for (var i = 0; i < op.length; i++) {
      if (op[i].value == pid) {
        op[i].disabled = true;
      } else {
        op[i].disabled = false;
      }
    }
  } else {
    for (var i = 0; i < op.length; i++) {
      op[i].disabled = false;
    }
  }
  if (childCount > 0) {
    document.getElementById('dept_slider').disabled = true;
    document.getElementById('dept_slider_parent').classList.add('w3-gray');
    document.getElementById('delete_btn').disabled = true;
  } else {
    document.getElementById('dept_slider').disabled = false;
    document.getElementById('dept_slider_parent').classList.remove('w3-gray');
    if (count !== 0) {
      document.getElementById('delete_btn').disabled = true;
    } else {
      document.getElementById('delete_btn').disabled = false;
    }
  }
  showSidebar('edit_position');

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
        window.location = 'index.php?Function=concom&admin=1';
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=concom', true);
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
  dblClick(isDiv, name, -1, division, 0, 0);
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
        window.location = 'index.php?Function=concom&admin=1';
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=concom', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('delete=' + id);

}
