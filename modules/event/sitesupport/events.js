/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar, hideSidebar, expandSection */
/* exported doImport, importConcom, deleteEvent, newEvent, saveEvent,
            editEvent, saveBadge, editBadge, newBadge, deleteBadge,
            expandEvent, saveCycle, newCycle, deleteMeeting,
            saveMeeting, editMeeting, newMeeting, reloadFromNeon */

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

var _deletedMeeting = 0;

function processMeetingDeletion() {
  confirmbox.close();

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
  xhttp.send('delete=' + _deletedMeeting);

}

function deleteMeeting(name, id) {
  _deletedMeeting = id;
  confirmbox.start(
    'Confirms Meeting Deletion',
    'Delete meeting ' + name + '?',
    processMeetingDeletion
  );

}

function newCycle() {
  showSidebar('edit_cycle');

}

function processNewCycle() {
  confirmbox.close();

  var data = {
    'From': document.getElementById('cycle_from').value,
    'To': document.getElementById('cycle_to').value,
  };
  var param = btoa(JSON.stringify(data));

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
  xhttp.send('cycle=' + param);

}

function saveCycle() {
  var from = document.getElementById('cycle_from').value;
  var to = document.getElementById('cycle_to').value;
  confirmbox.start(
    'Confirm New Annual Cycle',
    'Add Cycle [' + from + ' -> ' + to + '] ?',
    processNewCycle
  );

}

function expandEvent(name) {
  expandSection(name);

}

var _deletedBadge = 0;

function processBadgeDeletion() {
  confirmbox.close();

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
  xhttp.send('deleteBadge=' + _deletedBadge);

}

function deleteBadge(id, name) {
  _deletedBadge = id;
  confirmbox.start(
    'Confirms Badge Deletion',
    'Delete badge \'' + name + '\' ?',
    processBadgeDeletion
  );

}

function newBadge(id,name) {
  document.getElementById('badge_id').value = -1;
  document.getElementById('badge_event').value = id;
  document.getElementById('badge_name').value = 'New Badge';
  document.getElementById('badge_event_name').value = name;
  document.getElementById('badge_cost').value = 0;
  document.getElementById('badge_from').value = '0000-00-00';
  document.getElementById('badge_to').value = '0000-00-00';
  document.getElementById('badge_image').value = '';
  showSidebar('edit_badge');

}

function editBadge(data) {
  var badge = JSON.parse(atob(data));
  document.getElementById('badge_id').value = badge.Id;
  document.getElementById('badge_name').value = badge['Badge Name'];
  document.getElementById('badge_event').value = badge.Event;
  document.getElementById('badge_event_name').value = badge.EventName;
  document.getElementById('badge_cost').value = badge.Cost;
  document.getElementById('badge_from').value = badge.From;
  document.getElementById('badge_to').value = badge.To;
  document.getElementById('badge_image').value = badge.BackgroundImage;
  showSidebar('edit_badge');

}

function processNewBadge() {
  confirmbox.close();

  var data = {
    'Id': document.getElementById('badge_id').value,
    'Name': document.getElementById('badge_name').value,
    'Event': document.getElementById('badge_event').value,
    'Cost': document.getElementById('badge_cost').value,
    'From': document.getElementById('badge_from').value,
    'To': document.getElementById('badge_to').value,
    'Image': document.getElementById('badge_image').value,
  };
  var param = btoa(JSON.stringify(data));

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
  xhttp.send('badge=' + param);

}

function saveBadge() {
  var name = document.getElementById('badge_name').value;
  var evnt = document.getElementById('badge_event_name').value;
  confirmbox.start(
    'Confirm Event Badge',
    'Save Badge ' + name + ' for event  ' + evnt + ' ?',
    processNewBadge
  );

}

function editEvent(name, data) {
  var evnt = JSON.parse(atob(data));

  document.getElementById('event_id').value = evnt.Id;
  document.getElementById('event_name').value = name;
  document.getElementById('event_to').value = evnt.To;
  document.getElementById('event_from').value = evnt.From;
  showSidebar('edit_event');

}

function processNewEvent() {
  confirmbox.close();

  var data = {
    'Id': document.getElementById('event_id').value,
    'Name': document.getElementById('event_name').value,
    'To': document.getElementById('event_to').value,
    'From': document.getElementById('event_from').value,
  };
  var param = btoa(JSON.stringify(data));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      hideSidebar();
      location.reload();
    } else if (this.status == 404) {
      window.alert('Event Failed to Save, Check if proper cycle exists.');
      return;
    }
  };
  xhttp.open('POST', 'index.php?Function=event', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('event=' + param);

}

function saveEvent() {
  var To = document.getElementById('event_to').value;
  var From = document.getElementById('event_from').value;
  var name = document.getElementById('event_name').value;
  if (From === '') {
    window.alert('Event "From" date missing');
    return;
  } else if (To === '') {
    window.alert('Event "To" date missing');
    return;
  } else if (name === '') {
    window.alert('Event "Name" missing');
    return;
  }
  confirmbox.start(
    'Confirm Event',
    'Save Event "' + name + '" ?',
    processNewEvent
  );

}

function newEvent() {
  document.getElementById('event_id').value = -1;
  document.getElementById('event_name').value = 'New Event';
  document.getElementById('event_from').value = '0000-00-00';
  document.getElementById('event_to').value = '0000-00-00';
  showSidebar('edit_event');

}

var _deletedEvent = 0;

function processEventDeletion() {
  confirmbox.close();

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
  xhttp.send('deleteEvent=' + _deletedEvent);

}

function deleteEvent(id, name) {
  _deletedEvent = id;
  confirmbox.start(
    'Confirms Event Deletion',
    'Delete event \'' + name + '\' ?',
    processEventDeletion
  );

}

function importConcom(event) {
  document.getElementById('concom_event_id').value = event;
  var sel = document.getElementById('import_from');
  sel.selectedIndex = 0;
  for (var index = 0; index < sel.options.length; index++) {
    sel.options[index].disabled = false;
  }

  var idx = document.querySelector('#import_from option[value="' +
     event + '"]');
  idx.disabled = true;
  if (sel.selectedIndex == idx.index) {
    sel.selectedIndex++;
  }
  showSidebar('import_concom');

}

function doImport() {
  var to = document.getElementById('concom_event_id').value;
  var sel = document.getElementById('import_from');
  var from = sel.options[sel.selectedIndex].value;

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
  xhttp.send('duplicateConcom=' + to + '&from=' + from);
}
