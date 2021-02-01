/*
 * Javacript for the Registration badge checkin
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox */

import { RegConfiguration } from './modules/configuration.js';
import { RegTicket } from './modules/ticket.js';

'use strict';

class CheckinTicket extends RegTicket {
  constructor(data) {
    super(data);
  }

  displayCheckIn(e, page) {
    var n = document.createElement('DIV');
    e.append(n);
    n.classList.add('UI-container');
    n.classList.add('UI-padding');
    var s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = 'Badge&nbsp;#' + this.data.member.id + '&nbsp;-&nbsp;' +
       this.data.ticketType.Name;
    var br = document.createElement('BR');
    n.append(br);
    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = 'Badge Name ' + this.data.badgeName;
    br = document.createElement('BR');
    n.append(br);
    if (page.isOpen) {
      var l = document.createElement('LABEL');
      n.append(l);
      l.innerHTML = 'Check In&nbsp;';
      var cb = document.createElement('INPUT');
      cb.classList.add('UI-checkbox');
      cb.type = 'checkbox';
      cb.id = 'checkbox-' + this.data.id;
      cb.name = 'checking-checkbox';
      cb.onclick = function() {page.checkinOnOff();};
      n.append(cb);
    }
    return e;
  }

  displayBoardingPass(e, isOpen) {
    var obj = this;
    var n = document.createElement('DIV');
    e.append(n);
    n.classList.add('UI-container');
    n.classList.add('UI-padding');
    var s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    s.classList.add('UI-table-heading');
    n.append(s);
    s.innerHTML = '<h3>Boarding Pass</h3>';
    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = 'Badge&nbsp;#' + this.data.member.id + '&nbsp;-&nbsp;' +
      this.data.ticketType.Name;
    var br = document.createElement('BR');
    n.append(br);
    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = this.data.member.legalFirstName + '&nbsp;' +
                  this.data.member.legalLastName
    br = document.createElement('BR');
    n.append(br);
    var b = document.createElement('BUTTON');
    n.append(b);
    b.classList.add('UI-eventbutton');
    b.classList.add('UI-margin');
    b.innerHTML = 'Email Me';
    b.onclick = function() {
      confirmbox(
        'Are you sure you want to email this boarding pass to yourself?')
        .then(function() {
          obj.emailMe();
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        });
    };
    br = document.createElement('BR');
    n.append(br);
    b = document.createElement('BUTTON');
    n.append(b);
    b.classList.add('UI-eventbutton');
    b.classList.add('UI-margin');
    if (isOpen) {
      b.innerHTML = 'Badge Picked Up';
    } else {
      b.innerHTML = 'Badge Already Picked Up';
    }
    b.onclick = function() {
      confirmbox(
        'Do you physically have your badge in your hand?')
        .then(function() {
          obj.pickupBadge();
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        });
    };
    return e;
  }

  displayLostBadge(e) {
    var n = document.createElement('DIV');
    e.append(n);
    n.classList.add('UI-container');
    n.classList.add('UI-padding');
    var s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    s.classList.add('UI-table-heading');
    n.append(s);
    s.innerHTML = '<h3>Report Lost Badge</h3>';
    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = 'Badge&nbsp;#' + this.data.member.id + '&nbsp;-&nbsp;' +
      this.data.ticketType.Name;
    var br = document.createElement('BR');
    n.append(br);
    s = document.createElement('SPAN');
    s.classList.add('UI-padding');
    n.append(s);
    s.innerHTML = this.data.member.legalFirstName + '&nbsp;' +
                  this.data.member.legalLastName
    br = document.createElement('BR');
    n.append(br);
    var b = document.createElement('BUTTON');
    n.append(b);
    b.classList.add('UI-eventbutton');
    b.classList.add('UI-margin');
    b.innerHTML = 'Report Lost';
    var obj = this;
    b.onclick = function() {
      confirmbox('Are you sure you want to report this badge as lost?<br>' +
               '<b>There may be a fee involved in reprinting your badge.</b>')
        .then(function() {
          obj.lostBadge();
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        })
    };
    br = document.createElement('BR');
    return e;
  }

}

class CheckinPage {
  constructor(options) {
    this.settings = Object.assign(
      {
        'debug': true
      }, options);
    this.isOpen = false;
    this.pendingCheckin = [];
    this.readyCheckin = [];
    var obj = this;
    document.getElementById('checkinbutton').onclick = function() {
      obj.doCheckin();
    };
  }

  doCheckin() {
    var obj = this;
    confirmbox('Would you like to proceed with On-Line check In?')
      .then(function() {
        showSpinner();
        obj.readyCheckin.forEach(function(ticket) {
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
    var obj = this;
    var a = document.getElementsByName('checking-checkbox');
    var active = false;
    this.readyCheckin = [];
    a.forEach(function(e, i) {
      if (e.checked) {
        obj.readyCheckin.push(obj.pendingCheckin[i]);
        active = true;
      }
    });
    if (active) {
      document.getElementById('checkinbutton').classList.remove('UI-disabled')
    } else {
      document.getElementById('checkinbutton').classList.add('UI-disabled');
    }
  }

  createCard(id) {
    var e = document.createElement('DIV');
    e.id = 'ticket-' + id;
    e.classList.add('UI-border');
    e.classList.add('UI-center');
    e.classList.add('UI-container');
    e.classList.add('UI-padding');
    e.classList.add('UI-margin');
    return e;
  }

  handleTicket(ticket) {
    var e = this.createCard(ticket.data.id);
    if (ticket.data.boardingPassGenerated == null) {
      var p = document.getElementById('checkin');
      this.pendingCheckin.push(ticket.data);
      p.append(ticket.displayCheckIn(e, this));
      p.classList.remove('UI-hide');
      if (this.isOpen) {
        document.getElementById('checkin_button').classList.remove('UI-hide');
      }
    } else if (ticket.data.badgesPickedUp < 1) {
      p = document.getElementById('pickup');
      p.append(ticket.displayBoardingPass(e, this.isOpen));
      p.classList.remove('UI-hide');
    } else {
      p = document.getElementById('lost');
      p.append(ticket.displayLostBadge(e));
      p.classList.remove('UI-hide');
    }
  }

  processBadges() {
    var obj = this;
    if ('badgeNotice' in this.configuration &&
        this.configuration.badgeNotice.value.length > 0) {
      var e = document.getElementById('instructions');
      e.classList.remove('UI-hide');
      e.innerHTML = this.configuration.badgeNotice.value;
    }
    apiRequest('GET', 'registration/ticket/list',
      'maxResults=all&include=ticketType,member,registeredBy,event')
      .then(function(response) {
        var d = JSON.parse(response.responseText);
        if (Array.isArray(d.data)) {
          d.data.forEach((ticket) => {
            obj.handleTicket(new CheckinTicket(ticket));
          });
        } else {
          obj.handleTicket(new CheckinTicket(d));
        }

        var data = new URLSearchParams(window.location.search);
        if (data.get('highlight')) {
          var id = data.get('highlight');
          var elm  = document.getElementById('ticket-' + id);
          if (elm) {
            elm.style.backgroundColor = 'yellow';
            elm.scrollIntoView();
            elm = document.getElementById('checkbox-' + id);
            if (elm) {
              elm.checked = true;
              obj.checkinOnOff();
            }
          }
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
    this.configuration = new RegConfiguration(function() {
      showSpinner();
      apiRequest('GET', 'registration/open', null)
        .then(function(response) {
          var result = JSON.parse(response.responseText);
          var e = document.getElementById('regopen');
          obj.isOpen = result.open;
          if (result.open) {
            e.innerHTML = 'Open';
            e.classList.remove('UI-red');
            e.classList.add('UI-green');
          } else {
            e.innerHTML = 'Closed';
            e.classList.add('UI-red');
            e.classList.remove('UI-green');
          }
          obj.processBadges();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
          var e = document.getElementById('regopen');
          e.innerHTML = 'Closed';
          e.classList.add('UI-red');
          e.classList.remove('UI-green');
        });
    });
  }
}

function load() {
  new CheckinPage().load();
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
