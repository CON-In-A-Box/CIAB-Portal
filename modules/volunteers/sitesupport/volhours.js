/*
 * Javascript for volhours
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals showSpinner, hideSpinner, alertbox */
/* exported batchImport, batchDone, batchCancel, fileChange, batch */

'use strict';

function batch() {
  document.getElementById('csv_import').style.display = 'block';
}

function fileChange() {
  var file = document.getElementById('batch_file');
  var e = document.getElementById('batch_import');
  if (file.value !== '') {
    e.classList.remove('UI-disabled');
  } else {
    if (!e.classList.contains('UI-disabled')) {
      e.classList.add('UI-disabled');
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
      cell.className = 'UI-green';
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
        alertbox('404!');
      }
      else if (this.status == 409) {
        alertbox('409!');
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
