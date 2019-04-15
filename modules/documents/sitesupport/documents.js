/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* global confirmbox, hideSpinner, showSpinner */

'use strict';

function setSecret() {
  var code = btoa(document.getElementById('secret').value.trim());
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=documents', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  showSpinner();
  xhttp.send('&setSecret=' + code);
}

function authCode() {
  var code = document.getElementById('code').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=documents', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  showSpinner();
  xhttp.send('&setCode=' + code);
}

function setFolder() {
  var folder = document.getElementById('folder').value.trim();
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=documents', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  showSpinner();
  xhttp.send('&setFolder=' + folder);
}

var _element;
var _filename;
var _mime;

function getFile() {
  confirmbox.close();
  window.location =  'index.php?Function=documents&download=' + _element +
    '&filename=' + _filename + '&mime=' + _mime;
}

function downloadFile(element, filename, mime) {
  _element = element;
  _filename = filename;
  _mime = mime;
  confirmbox.start('Download File',
    'Are you sure you want to download ' + _filename, getFile);
}

function loadFiles(path) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSpinner();
      var fileTable = document.getElementById('file_table');
      var folderTable = document.getElementById('folder_table');
      var fileCount = 0;
      var folderCount = 0;
      var resp = JSON.parse(this.response);
      if (resp.path) {
        document.getElementById('root_path').innerHTML = resp.path;
      } else {
        document.getElementById('root_path').innerHTML = '';
      }
      resp.output.forEach(function(element) {
        var row;
        var icon;
        if (element[3] == 'application/vnd.google-apps.folder') {
          row = folderTable.insertRow(-1);
          icon = '<i class="fa fa-folder"></i> ';
          folderCount += 1;
        } else {
          row = fileTable.insertRow(-1);
          icon = '<i class="fa fa-file"></i> ';
          fileCount += 1;
        }
        var cell = row.insertCell(0);
        cell.innerHTML = icon + '<span>' + element[0] + '</span>';
        row.classList.add('event-hover-secondary');
        if (element[3] == 'application/vnd.google-apps.folder') {
          row.setAttribute('onclick', 'window.location="index.php?' +
            'Function=documents&path=' + element[0] + '";');
        } else {
          row.setAttribute('onclick', 'downloadFile("' + element[2] + '", "' +
                element[0] + '", "' + element[3] + '");');
          cell = row.insertCell(1);
          cell.innerHTML = element[1];
        }
      });
      if (folderCount === 0) {
        folderTable.classList.add('UI-hide');
      } else {
        folderTable.classList.remove('UI-hide');
      }
      if (fileCount === 0) {
        fileTable.classList.add('UI-hide');
      } else {
        fileTable.classList.remove('UI-hide');
      }
    }
  };
  showSpinner();
  xhttp.open('GET', 'index.php?Function=documents&loadFiles=' + path, true);
  xhttp.send();
}
