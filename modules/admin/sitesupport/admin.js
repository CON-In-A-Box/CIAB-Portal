/* jshint browser: true */
/* jshint -W097 */
/* exported downloadLog, setField, addField, removeAdmin */

function downloadLog() {
  window.location = 'index.php?Function=admin&downloadLog=db';

}

function setField(field) {
  var value = document.getElementById('config_' + field).value;
  value = btoa(value);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('&setField=' + field + '&value=' + value);

}

function addField() {
  var field = document.getElementById('new_Field').value;
  var value = document.getElementById('new_Value').value;
  value = btoa(value);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('&newField=' + field + '&value=' + value);

}

function removeAdmin() {

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  };
  xhttp.open('POST', 'index.php?Function=admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('&removeAdmin=true');

}
