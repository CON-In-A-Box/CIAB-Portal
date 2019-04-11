/*
 * Javascript for volhours
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals userLookup, showSpinner, hideSpinner */

'use strict';

var volPast = null;

function calculateHours() {
  var hours = Number(document.getElementById('hours').value);
  var am = Number(document.getElementById('minutes').value);
  var creditHours = 0;
  var creditMinutes = hours * document.getElementById('modifier').value * 60 +
                      am * document.getElementById('modifier').value;

  var r = Math.floor(creditMinutes / 60);
  if (r > 0) {
    creditHours += r;
    creditMinutes = creditMinutes % 60;
  }

  var tag = ' hours';
  var mtag = ' minutes';
  if (creditHours == 1) {
    tag = ' hour';
  }
  if (creditMinutes == 1) {
    mtag = ' minute';
  }
  document.getElementById('totalhours').innerHTML = '';
  if (creditHours > 0) {
    document.getElementById('totalhours').innerHTML = creditHours + tag;
  }
  if (creditMinutes > 0) {
    document.getElementById('totalhours').innerHTML += ' ' + creditMinutes +
                                                       mtag ;
  }
  document.getElementById('actualhours').value = Number(hours) +
                                                 Number(am / 60);
  checkHours();
}

function markEndTime(invalid) {
  if (invalid) {
    document.getElementById('end_date').classList.add('UI-red');
    document.getElementById('end_hours').classList.add('UI-red');
    document.getElementById('end_minutes').classList.add('UI-red');
    document.getElementById('end_ampm').classList.add('UI-red');
  } else {
    document.getElementById('end_date').classList.remove('UI-red');
    document.getElementById('end_hours').classList.remove('UI-red');
    document.getElementById('end_minutes').classList.remove('UI-red');
    document.getElementById('end_ampm').classList.remove('UI-red');
  }
}

function resetForm() {
  userLookup.clear();
  document.getElementById('submitbtn').disabled = true;
  document.getElementById('volunteername').innerHTML = 'a Volunteer';
  document.getElementById('lookupname').innerHTML = '<span></span>';

  /* Clean out old highlighted departments */
  var depts = document.getElementById('department');
  for (var i = 0; i < depts.length; i++) {
    if (depts[i].text[0] == '*' || depts[i].text[0] == '-') {
      depts[i + 1].selected = true;
      depts.remove(i);
      i--;
    }
  }
  document.getElementById('message').innerHTML = '';
  markEndTime(false);
}

function formatTime(time) {

  var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday',
              'Saturday'];
  var pmam = null;
  if (time.getHours() >= 12) {
    pmam = ' PM';
  } else {
    pmam = ' AM';
  }
  var hours = time.getHours() % 12;
  if (hours === 0) {
    hours = 12;
  }
  var min = time.getMinutes();
  if (min < 10) {
    min = '0' + min;
  } else {
    min = min.toString();
  }

  return (days[time.getDay()] + ' ' + hours.toString() + ':' + min + pmam);
}

function buildEndDate() {
  var endHours = Number(document.getElementById('end_hours').value);
  var endMins = Number(document.getElementById('end_minutes').value);
  var endAmpm = document.getElementById('end_ampm').value;

  var end = document.getElementById('end_date').value;
  var str = end + ' ' + endHours + ':' + endMins + ':00 ' + endAmpm;
  return new Date(str);
}

function checkHours() {
  markEndTime(false);
  document.getElementById('message').innerHTML = '';

  if (volPast && volPast.length) {
    var hours = Number(document.getElementById('hours').value);
    var mins  = Number(document.getElementById('minutes').value);

    var newEnd = buildEndDate();
    var newBegin = buildEndDate();
    newBegin.setHours(newBegin.getHours() - hours);
    newBegin.setMinutes(newBegin.getMinutes() - mins);

    for (var i = 0; i < volPast.length; i++) {
      var _shift = volPast[i];
      var _end = new Date(_shift['End Date Time']);
      var _begin = new Date(_shift['End Date Time']);
      _begin.setHours(_begin.getHours() - Math.floor(_shift['Actual Hours']));
      _begin.setMinutes(_begin.getMinutes() -
        Math.floor((_shift['Actual Hours'] % 1) * 60));

      if ((newBegin < _end && newBegin > _begin) ||
          (newEnd < _end && newEnd > _begin) ||
          (newEnd >= _end && newBegin <= _begin)) {
        markEndTime(true);
        document.getElementById('message').innerHTML = 'Overlapping with ' +
            _shift['Department Worked'] + ' ( ' + formatTime(_begin) +
            ' - ' + formatTime(_end) + ' ) ';
        break;
      }
    }
  }
}

function handleResult(origin, response) {
  var e = document.getElementById('userLookup_dropdown');
  if (!e.classList.contains('w3-hide')) {
    e.classList.add('w3-hide');
  }
  markEndTime(false);
  document.getElementById('message').innerHTML = '';

  var name = response['First Name'] + ' ' + response['Last Name'];
  var uid = response.Id;
  if (response.ConCom) {
    userLookup.markFailure();
    document.getElementById('submitbtn').disabled = true;
    document.getElementById('volunteername').innerHTML = 'a Volunteer';
    document.getElementById('lookupname').innerHTML =
        '<span class="UI-red w3-large">' + name +
        ' is on ConCom (' + uid + ')</span>';
  } else {
    userLookup.clearFailure();
    userLookup.set(uid);
    document.getElementById('submitbtn').disabled = false;
    document.getElementById('volunteername').innerHTML = name + ' (' +
        uid + ')';
    document.getElementById('lookupname').innerHTML =
        '<span class="w3-large">' + name + ' (' + uid + ')<span>';

    var depts = document.getElementById('department');

    /* Clean out old highlighted departments */
    for (i = 0; i < depts.length; i++) {
      if (depts[i].text[0] == '*' || depts[i].text[0] == '-') {
        depts[i + 1].selected = true;
        depts.remove(i);
        i--;
      }
    }
    if ('volunteer' in response) {
      var past = [];
      var option = document.createElement('option');
      option.text = '------------------';
      option.disabled = true;
      depts.add(option, 0);

      for (var i = 0, len = response.volunteer.length; i < len; i++) {
        var dept = response.volunteer[i];
        if (past.indexOf(dept['Department Worked']) == -1) {
          option = document.createElement('option');
          option.selected = true;
          option.value = dept['Department Worked'];
          option.text = '* ' + dept['Department Worked'];
          depts.add(option, 0);
          past.push(dept['Department Worked']);
        }
      }
    }
    if ('volunteer' in response) {
      volPast = response.volunteer;
      checkHours();
    }
  }
}

function onFail(target, resp, name, code) {
  userLookup.markFailure();
  document.getElementById('submitbtn').disabled = true;
  document.getElementById('volunteername').innerHTML = 'a Volunteer';

  if (code == 404) {
    document.getElementById('lookupname').innerHTML =
        '<span class="UI-red w3-large">' + name + ' not found.</span>';
  }
  else if (code == 409) {
    document.getElementById('lookupname').innerHTML =
      '<span class="UI-red w3-large">There are too many matches.</span>';
  }
  else {
    document.getElementById('lookupname').innerHTML = name +
        '<span class="UI-red w3-large">invalid name lookup.(' + code +
        ')</span>';
  }
}

function batch() {
  document.getElementById('csv_import').style.display = 'block';
}

function fileChange() {
  var file = document.getElementById('batch_file');
  var e = document.getElementById('batch_import');
  if (file.value !== '') {
    e.classList.remove('w3-disabled');
  } else {
    if (!e.classList.contains('w3-disabled')) {
      e.classList.add('w3-disabled');
    }
  }
}

function batchCancel() {
  document.getElementById('csv_import').style.display = 'none';
}

function reportImport(report) {
  var table = document.getElementById('import_report');
  var obj = JSON.parse(report);

  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  obj.forEach(function(item) {
    var row = table.insertRow();
    var cell = row.insertCell(0);
    if (item.indexOf(': success:') != -1) {
      cell.className = 'w3-green';
    } else {
      cell.className = 'UI-red';
    }
    cell.innerHTML = item;
  });
  document.getElementById('csv_import_report').style.display = 'block';
}

function batchDone() {
  document.getElementById('csv_import_report').style.display = 'none';
}

function batchImport() {
  document.getElementById('csv_import').style.display = 'none';
  showSpinner();

  var files = document.getElementById('batch_file');
  var upload = files.files[0];
  var reader = new FileReader();
  reader.onloadend = function() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        hideSpinner();
        reportImport(this.responseText);
      }
      else if (this.status == 404) {
        window.alert('404!');
      }
      else if (this.status == 409) {
        window.alert('409!');
      }
      if (this.readyState != 1) {
        hideSpinner();
      }
    };
    xhttp.open('POST', 'index.php?Function=volunteers/enter/upload', true);
    xhttp.setRequestHeader('Content-type', 'text/csv');
    xhttp.send(this.result);
  };
  reader.readAsText(upload);
}
