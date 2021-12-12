/*
 * Javacript for the Registration badge boarding passes
 */

/* jshint browser: true */
/* jshint -W097 */
/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox, URLSearchParams */

'use strict';

import { RegPage } from './modules/page.js';
import { RegTicket } from './modules/ticket.js';

class BoardingPass extends RegTicket {
  constructor(data) {
    super(data);
  }

  display(e, isOpen) {
    const template = `
    <div>
      Badge&nbsp;#${this.data.member.id}&nbsp;-&nbsp;${this.data.ticket_type.name}
    </div>
    <div>
      ${this.data.member.legal_first_name}&nbsp;${this.data.member.legal_last_name}
    </div>
    <div><button class="UI-eventbutton" id="email-${this.data.id}">Email</button></div>
    <div><button class="UI-eventbutton" id="pickup-${this.data.id}">Badge Picked Up</button></div>
    `;

    e.insertAdjacentHTML('beforeend', template);

    e.querySelector(`#email-${this.data.id}`).addEventListener('click', () => {
      confirmbox(
        'Are you sure you want to email this boarding pass to yourself?')
        .then(() => this.emailMe())
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        });
      }
    );

    if (!isOpen) {
      document.getElementById(`pickup-${this.data.id}`).innerHTML = 'Badge Already Picked Up';
    }

    e.querySelector(`#pickup-${this.data.id}`).addEventListener('click', () => {
      confirmbox(
        'Do you physically have your badge in your hand?')
        .then(() => this.pickupBadge())
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        });
      }
    );

    return e;
  }
}

class BoardingPassPage extends RegPage {
  constructor(options) {
    super(options);
  }

  createCard(id) {
    var e = document.createElement('DIV');
    e.id = 'ticket-' + id;
    e.classList.add('REG-boarding-pass');
    return e;
  }

  handleTicket(ticket) {
    var e = this.createCard(ticket.data.id);
    if (ticket.data.badges_picked_up < 1) {
      var p = document.getElementById('pickup');
      p.append(ticket.display(e, this.isOpen));
      p.classList.remove('UI-hide');
    }
  }

  processBadges() {
    apiRequest('GET', 'registration/ticket/list', 'max_results=all&checked_in=1')
      .then((response) => {
        var d = JSON.parse(response.responseText);
        if (Array.isArray(d.data)) {
          d.data.forEach((ticket) => {
            this.handleTicket(new BoardingPass(ticket));
          });
        }

        hideSpinner();
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      });
  }
}

var page = new BoardingPassPage();

function load() {
  page.load();
  window.page = page;
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
