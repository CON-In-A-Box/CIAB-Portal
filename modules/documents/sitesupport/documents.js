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

function loadFiles() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSpinner();
      var table = document.getElementById('file_table');
      var resp = JSON.parse(this.response);
      resp.forEach(function(element) {
        var row = table.insertRow(-1);
        row.classList.add('event-hover-primary');
        row.setAttribute('onclick', 'downloadFile("' + element[2] + '", "' +
            element[0] + '", "' + element[3] + '");');
        var cell = row.insertCell(0);
        cell.innerHTML = element[0];
        cell = row.insertCell(1);
        cell.innerHTML = element[1];
      });
    }
  };
  showSpinner();
  xhttp.open('GET', 'index.php?Function=documents&loadFiles=1', true);
  xhttp.send();
}
