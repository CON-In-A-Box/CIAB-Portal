/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar */

function expandSection(id) {
  var x = document.getElementById(id);
  var y = document.getElementById(id + '_arrow');
  if (x.className.indexOf('w3-show') == -1) {
    x.className += ' w3-show';
    y.className = 'fa fa-caret-up';
  } else {
    x.className = x.className.replace(' w3-show', '');
    y.className = 'fa fa-caret-down';
  }

}

function reloadFromNeon() {
  window.location = 'index.php?Function=event&reloadFromNeon=1';

}

function newMeeting() {
  var today = new Date();
  var dd = today.getDate();
  var mm = today.getMonth() + 1;
  var yyyy = today.getFullYear();
  if (dd < 10) {
    dd = '0' + dd;
  }
  if (mm < 10) {
    mm = '0' + mm;
  }
  document.getElementById('meet_id').value = -1;
  document.getElementById('meet_name').value = 'New Meeting';
  document.getElementById('meet_date').value = yyyy + '-' + mm + '-' + dd;
  document.getElementById('meet_event').selectedIndex = '0';
  showSidebar('edit_meeting');

}

function editMeeting(data) {
  var meeting = JSON.parse(atob(data));
  document.getElementById('meet_id').value = meeting.Id;
  document.getElementById('meet_name').value = meeting.Name;
  document.getElementById('meet_event').value = meeting.EventID;
  document.getElementById('meet_date').value = meeting.Date;
  showSidebar('edit_meeting');

}

function processMeeting() {
  confirmbox.close();

  var data = {
      'Id': document.getElementById('meet_id').value,
      'Name': document.getElementById('meet_name').value,
      'EventID': document.getElementById('meet_event').value,
      'Date': document.getElementById('meet_date').value,
    };
  var param = JSON.stringify(data);
  console.log(param);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        hideSidebar();
        location.reload();
      } else if (this.status == 404) {
        window.alert('404!');
      } else if (this.status == 409) {
        window.alert('409!');
      }
    };
  xhttp.open('POST', 'index.php?Function=event', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('modify=' + btoa(param));

}

function saveMeeting() {
  confirmbox.start(
      'Confirms Meeting Details',
      'Are the meeting details correct?',
      processMeeting
  );

}
