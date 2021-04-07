/*
 * Javacript for the Registration badge management
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox, showSidebar */

import { RegConfiguration } from './modules/configuration.js';
import { RegTicket } from './modules/ticket.js';

'use strict';

class ManageTicket extends RegTicket {
  constructor(data) {
    super(data);
  }

  displayMember(e, isOpen) {
    var n = document.createElement('DIV');
    e.append(n);
    n.classList.add('UI-container');
    n.classList.add('UI-padding');
    var s = document.createElement('H3');
    s.classList.add('UI-padding');
    s.classList.add('UI-table-heading');
    s.classList.add('UI-bold');
    n.append(s);
    s.innerHTML = this.data.member.legalFirstName + '&nbsp;' +
                  this.data.member.legalLastName
    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = 'Badge&nbsp;#' + this.data.member.id;
    var br = document.createElement('BR');
    n.append(br);

    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = this.data.badge_name;
    br = document.createElement('BR');
    n.append(br);

    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = this.data.ticket_type.name;
    br = document.createElement('BR');
    n.append(br);

    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = 'Badge Picked Up&nbsp;';
    var cb = document.createElement('INPUT');
    cb.type = 'checkbox';
    cb.classList.add('UI-checkbox');
    cb.disabled = true;
    cb.checked = (this.data.badges_picked_up > 0);
    s.append(cb);

    br = document.createElement('BR');
    n.append(br);
    br = document.createElement('BR');
    n.append(br);

    var obj = this;
    if (this.data.badges_picked_up < 1) {
      s = document.createElement('A');
      n.append(s);
      s.href = 'javascript:;';
      s.onclick = function() {
        document.getElementById('badge_name').value = obj.data.badge_name;
        document.getElementById('contact').value = obj.data.emergency_contact;
        document.getElementById('save_button').onclick = function() {
          obj.data.badge_name = document.getElementById('badge_name').value;
          obj.data.emergency_contact = document.getElementById('contact').value;
          obj.updateBadge();
        }
        showSidebar('modify_ticket');
      };
      s.innerHTML = 'Edit Registration';
      br = document.createElement('BR');
      n.append(br);
    }

    s = document.createElement('A');
    n.append(s);
    if (this.data.boarding_pass_generated == null) {
      if (isOpen) {
        s.innerHTML = 'Check in';
        s.href = 'index.php?Function=registration/checkin&highlight=' +
          this.data.id;
      } else {
        s.innerHTML = 'Check in (closed)';
        s.href = 'javascript:;';
        s.disabled = true;
      }
    } else if (this.data.badges_picked_up < 1) {
      if (isOpen) {
        s.innerHTML = 'Boarding Pass';
        s.href = 'index.php?Function=registration/checkin&highlight=' +
          this.data.id;
      } else {
        s.innerHTML = 'Boarding Pass (closed)';
        s.href = 'javascript:;';
        s.disabled = true;
      }
    } else {
      s.innerHTML = 'Report Lost Badge';
      s.href = 'javascript:;';
      s.onclick = function() {
        confirmbox('Are you sure you want to report this badge as lost?<br>' +
                 '<b>There may be a fee involved in reprinting your badge.</b>')
          .then(function() {
            obj.lostBadge();
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
          })
      };
    }


    if (this.data.boarding_pass_generated != null &&
        this.data.badges_picked_up < 1) {
      br = document.createElement('BR');
      n.append(br);
      s = document.createElement('A');
      n.append(s);
      s.innerHTML = 'Email me this Boarding Pass';
      s.href = 'javascript:;';
      s.onclick = function() {
        confirmbox(
          'Are you sure you want to email this boarding pass to yourself?')
          .then(function() {
            obj.emailMe();
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
          });
      };
    }

    br = document.createElement('BR');
    n.append(br);

    return e;
  }

  handleTicket(e, isOpen) {
    var p = document.getElementById('members');
    p.append(this.displayMember(e, isOpen));
  }

}

class ManagePage {
  constructor(options) {
    this.settings = Object.assign(
      {
        'debug': true
      }, options);
    this.isOpen = false;
  }

  doCheckin() {
    confirmbox('Would you like to proceed with On-Line check In?')
      .then(function() {
        showSpinner();
        this.readyCheckin.forEach(function(ticket) {
          apiRequest('PUT', 'registration/ticket/' + ticket.id +
                            '/checkin','')
            .then(function(response) {
              hideSpinner();
              location.reload();
              console.log(response);
            })
            .catch(function(response) {
              hideSpinner();
              if (response instanceof Error) { throw response; }
            })
        })
      })
      .catch(function(response) {
        if (response instanceof Error) { throw response; }
      });
  }

  checkinOnOff() {
    var a = document.getElementsByName('checking-checkbox');
    var active = false;
    this.readyCheckin = [];
    a.forEach(function(e, i) {
      if (e.checked) {
        this.readyCheckin.push(this.pendingCheckin[i]);
        active = true;
      }
    });
    if (active) {
      document.getElementById('checkinbutton').classList.remove('UI-disabled')
    } else {
      document.getElementById('checkinbutton').classList.add('UI-disabled');
    }
  }

  createCard() {
    var e = document.createElement('DIV');
    e.classList.add('UI-border');
    e.classList.add('UI-center');
    e.classList.add('UI-container');
    e.classList.add('UI-padding');
    e.classList.add('UI-margin');
    return e;
  }

  processBadges() {
    var obj = this;
    apiRequest('GET', 'registration/ticket/list', 'max_results=all')
      .then(function(response) {
        var d = JSON.parse(response.responseText);
        if (Array.isArray(d.data)) {
          d.data.forEach((ticket) => {
            var e = obj.createCard();
            new ManageTicket(ticket).handleTicket(e, obj.isOpen);
          });
        } else {
          var e = obj.createCard();
          new ManageTicket(d).handleTicket(e, obj.isOpen);
        }
        hideSpinner();
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      })
  }

  load() {
    var obj = this;
    showSpinner();
    this.configuration = new RegConfiguration(function() {
      apiRequest('GET', 'registration/open', null)
        .then(function(response) {
          var result = JSON.parse(response.responseText);
          obj.isOpen = result.open;
          obj.processBadges();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        });
    });
  }
}

function load() {
  new ManagePage().load();
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
