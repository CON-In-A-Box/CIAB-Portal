/*
 * Javacript for the Registration badge checkin
 */

/* jshint browser: true */
/* jshint -W097 */
/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox */

'use strict';

import { RegTicket } from './modules/ticket.js';
import { RegPage } from './modules/page.js';

class CheckinTicket extends RegTicket {
  constructor(data) {
    super(data);
  }

  display(e, page) {
    var template = `
    <div class="REG-checkin-text">
      Badge&nbsp;#${this.data.member.id}&nbsp;-&nbsp;${this.data.ticket_type.name}
    </div>
    <div class="REG-checkin-text">Badge Name ${this.data.badge_name}</div>
    `;

    if (page.isOpen) {
      template += `
        <label for="checkin-checkbox">Check In</label>
        <input name="checkin-checkbox" class="UI-checkbox" type="checkbox" id="checkbox-${this.data.id}"
               onclick="page.checkinOnOff()" />
      `;
    }

    e.insertAdjacentHTML('beforeend', template);
    return e;
  }
}

class CheckinPage extends RegPage {
  constructor(options) {
    super(options);
    this.pendingCheckin = [];
    this.readyCheckin = [];
    document.getElementById('checkinbutton').onclick = ()=> {
      this.doCheckin();
    };
  }

  doCheckin() {
    confirmbox('Would you like to proceed with On-Line check In?')
      .then(() => {
        showSpinner();
        this.readyCheckin.forEach(function(ticket) {
          apiRequest('PUT', 'registration/ticket/' + ticket.id +
                            '/checkin','')
            .then(function() {
              hideSpinner();
              location.assign('/index.php?Function=main');
            })
            .catch(function(response) {
              hideSpinner();
              if (response instanceof Error) { throw response; }
            });
        });
      })
      .catch(function(response) {
        if (response instanceof Error) { throw response; }
      });
  }

  checkinOnOff() {
    var a = document.getElementsByName('checkin-checkbox');
    var active = false;
    this.readyCheckin = [];
    a.forEach((e, i) => {
      if (e.checked) {
        this.readyCheckin.push(this.pendingCheckin[i]);
        active = true;
      }
    });
    if (active) {
      document.getElementById('checkinbutton').classList.remove('UI-disabled');
    } else {
      document.getElementById('checkinbutton').classList.add('UI-disabled');
    }
  }

  createCard(id) {
    var e = document.createElement('DIV');
    e.id = 'ticket-' + id;
    e.classList.add('REG-checkin-badge');
    return e;
  }

  handleTicket(ticket) {
    var e = this.createCard(ticket.data.id);
    var p = document.getElementById('checkin');
    this.pendingCheckin.push(ticket.data);
    p.append(ticket.display(e, this));
    p.classList.remove('UI-hide');
    if (this.isOpen) {
      document.getElementById('checkin_button').classList.remove('UI-hide');
    }
  }

  processBadges() {
    if ('badgeNotice' in this.configuration &&
        this.configuration.badgeNotice.value.length > 0) {
      var e = document.getElementById('instructions');
      e.classList.remove('UI-hide');
      e.innerHTML = this.configuration.badgeNotice.value;
    }
    apiRequest('GET', 'registration/ticket/list', 'max_results=all&checked_in=0')
      .then((response) => {
        var d = JSON.parse(response.responseText);
        if (Array.isArray(d.data)) {
          d.data.forEach((ticket) => {
            this.handleTicket(new CheckinTicket(ticket));
          });
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
              this.checkinOnOff();
            }
          }
        }

        hideSpinner();
      })
      .catch((response) => {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      });
  }
}

var page = new CheckinPage();

function load() {
  page.load();
  window.page = page;
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
