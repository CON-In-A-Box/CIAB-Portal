/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar, hideSidebar, expandSection, alertbox,
           basicBackendRequest, apiRequest, showSpinner, hideSpinner */
/* exported doImport, importConcom, deleteEvent, newEvent, saveEvent,
            editEvent, saveBadge, editBadge, newBadge, deleteBadge,
            expandEvent, saveCycle, newCycle, deleteMeeting,
            saveMeeting, editMeeting, newMeeting, reloadFromNeon */

function basicEventRequest(parameter, finish) {
  basicBackendRequest('POST', 'event', parameter, finish);
}

function reloadFromNeon() {
  window.location = 'index.php?Function=event&reloadFromNeon=1';
}

function newMeeting() {
  var today = new Date();
  var dd = ('0' + today.getDate()).slice(-2);
  var mm = ('0' + (today.getMonth() + 1)).slice(-2);
  var yyyy = today.getFullYear();
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

function saveMeeting() {
  confirmbox(
    'Confirms Meeting Details',
    'Are the meeting details correct?').then(function() {
    var data = {
      'Id': document.getElementById('meet_id').value,
      'Name': document.getElementById('meet_name').value,
      'EventID': document.getElementById('meet_event').value,
      'Date': document.getElementById('meet_date').value,
    };
    var param = JSON.stringify(data);
    basicEventRequest('modify=' + btoa(param), function() {
      hideSidebar();
      location.reload();
    });
  });
}

function deleteMeeting(name, id) {
  confirmbox(
    'Confirms Meeting Deletion',
    'Delete meeting ' + name + '?').then(function() {
    basicEventRequest('delete=' + id, function() {
      hideSidebar();
      location.reload();
    });
  });
}

function newCycle() {
  document.getElementById('cycle_id').value = -1;

  var day = new Date(document.getElementById('last_cycle').value);
  var dd = ('0' + (day.getDate() + 2)).slice(-2);
  var dd2 = ('0' + (day.getDate() + 1)).slice(-2);
  var mm = ('0' + (day.getMonth() + 1)).slice(-2);
  var yyyy = day.getFullYear();
  var yyyy2 = day.getFullYear() + 1;

  document.getElementById('cycle_from').value = yyyy + '-' + mm + '-' + dd;
  document.getElementById('cycle_to').value = yyyy2 + '-' + mm + '-' + dd2;
  showSidebar('edit_cycle');
}

function saveCycle() {
  var from = document.getElementById('cycle_from').value;
  var to = document.getElementById('cycle_to').value;
  var id = document.getElementById('cycle_id').value;
  var title = 'Confirm New Annual Cycle';
  var msg = 'Add Cycle [' + from + ' -> ' + to + '] ?';
  var method = 'POST';
  var target = 'cycle';
  if (id != -1) {
    title = 'Confirm Modified Annual Cycle';
    msg = 'Modify Cycle [' + from + ' -> ' + to + '] ?';
    method = 'PUT';
    target += '/' + id;
  }
  confirmbox(title, msg).then(function() {
    var data = 'date_from=' + document.getElementById('cycle_from').value +
    '&' + 'date_to=' + document.getElementById('cycle_to').value;
    apiRequest(method, target, data).then(function() {
      location.reload();
    })
      .catch(function() {
        alert('Add/Modify cycle Failed');
        hideSidebar();
      });
  });
}

function expandEvent(name) {
  expandSection(name);

}

function deleteBadge(id, name) {
  confirmbox(
    'Confirms Badge Deletion',
    'Delete badge \'' + name + '\' ?').then(function() {
    basicEventRequest('deleteBadge=' + id, function() {
      hideSidebar();
      location.reload();
    });
  });
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

function saveBadge() {
  var name = document.getElementById('badge_name').value;
  var evnt = document.getElementById('badge_event_name').value;
  confirmbox(
    'Confirm Event Badge',
    'Save Badge ' + name + ' for event  ' + evnt + ' ?').then(function() {
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
    basicEventRequest('badge=' + param, function() {
      hideSidebar();
      location.reload();
    });
  });
}

function editEvent(name, data) {
  var evnt = JSON.parse(atob(data));

  document.getElementById('event_id').value = evnt.Id;
  document.getElementById('event_name').value = name;
  document.getElementById('event_to').value = evnt.To;
  document.getElementById('event_from').value = evnt.From;
  showSidebar('edit_event');

}

function saveEvent() {
  var To = document.getElementById('event_to').value;
  var From = document.getElementById('event_from').value;
  var name = document.getElementById('event_name').value;
  if (From === '') {
    alertbox('Event "From" date missing');
    return;
  } else if (To === '') {
    alertbox('Event "To" date missing');
    return;
  } else if (name === '') {
    alertbox('Event "Name" missing');
    return;
  }
  confirmbox(
    'Confirm Event',
    'Save Event "' + name + '" ?').then(function() {
    var data = {
      'Id': document.getElementById('event_id').value,
      'Name': document.getElementById('event_name').value,
      'To': document.getElementById('event_to').value,
      'From': document.getElementById('event_from').value,
    };
    var param = btoa(JSON.stringify(data));
    basicEventRequest('event=' + param, function() {
      hideSidebar();
      location.reload();
    });
  });
}

function newEvent() {
  document.getElementById('event_id').value = -1;
  document.getElementById('event_name').value = 'New Event';
  document.getElementById('event_from').value = '0000-00-00';
  document.getElementById('event_to').value = '0000-00-00';
  showSidebar('edit_event');

}

function deleteEvent(id, name) {
  confirmbox(
    'Confirms Event Deletion',
    'Delete event \'' + name + '\' ?').then(function() {
    basicEventRequest('deleteEvent=' + id, function() {
      hideSidebar();
      location.reload();
    });
  });
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
  basicEventRequest('duplicateConcom=' + to + '&from=' + from, function() {
    hideSidebar();
    location.reload();
  });
}

function editCycle(data) {
  document.getElementById('cycle_id').value = data.id;
  document.getElementById('cycle_from').value = data.date_from;
  document.getElementById('cycle_to').value = data.date_to;
  showSidebar('edit_cycle');
}

function loadEvents() {
  showSpinner();
  apiRequest('GET', 'cycle', 'max_results=all')
    .then(function(response) {
      hideSpinner();
      var result = JSON.parse(response.responseText);
      if (result.data.length > 0) {
        var sorted = result.data.sort(function(a,b) {
          return Date.parse(b.date_from) - Date.parse(a.date_from);
        });
        var table = document.getElementById('cycle_list');
        document.getElementById('last_cycle').value = sorted[0].date_to;
        sorted.forEach(function(data) {
          var line = document.createElement('DIV');
          line.classList.add('UI-table-row');
          var f = document.createElement('DIV');
          f.classList.add('UI-table-cell');
          f.appendChild(document.createTextNode(data.id));
          line.appendChild(f);
          f = document.createElement('DIV');
          f.classList.add('UI-table-cell');
          f.appendChild(document.createTextNode(data.date_from));
          line.appendChild(f);
          f = document.createElement('DIV');
          f.classList.add('UI-table-cell');
          f.appendChild(document.createTextNode(data.date_to));
          line.appendChild(f);

          var to = new Date(data.date_to);
          var from  = new Date(data.date_from);
          var today = new Date();
          f = document.createElement('DIV');
          f.classList.add('UI-table-cell');
          var em = document.createElement('EM');
          if (today > to) {
            em.classList.add('fas');
            em.classList.add('fa-lock');
          } else if (today >= from && today <= to) {
            em.classList.add('far');
            em.classList.add('fa-star');
            line.addEventListener('click', function() {
              editCycle(data);
            });
          } else {
            em.classList.add('fas');
            em.classList.add('fa-arrow-right');
            line.addEventListener('click', function() {
              editCycle(data);
            });
          }
          f.appendChild(em);
          line.appendChild(f);

          table.append(line);
        });
      }
    })
    .catch(function() {
      hideSpinner();
    });
}

if (window.addEventListener) {
  window.addEventListener('load', loadEvents);
} else {
  window.attachEvent('onload', loadEvents);
}
