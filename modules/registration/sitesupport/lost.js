/*
 * Javacript for the Registration lost badges
 */

/* jshint browser: true */
/* jshint -W097 */
/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox, URLSearchParams */

'use strict';

import { RegPage } from './modules/page.js';
import { RegTicket } from './modules/ticket.js';

class LostBadge extends RegTicket {
  constructor(data) {
    super(data);
  }

  display(e) {
    var template =`
    <div class="UI-container">
      <div>
        Badge&nbsp;#${this.data.member.id}&nbsp;-&nbsp;${this.data.ticket_type.name}
      </div>
      <div>
        ${this.data.member.legal_first_name}&nbsp;${this.data.member.legal_last_name}
      </div>
      <button class="UI-eventbutton" id="lost-${this.data.id}">Report Lost</button>


    `;

    e.insertAdjacentHTML('beforeend', template);

    e.querySelector(`#lost-${this.data.id}`).addEventListener('click', () => {
      confirmbox('Are you sure you want to report this badge as lost?<br>' +
               '<b>There may be a fee involved in reprinting your badge.</b>')
        .then(() => this.lostBadge())
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    });

    return e;
  }
}

class LostBadgePage extends RegPage {
  constructor(options) {
    super(options);
  }

  createCard(id) {
    var e = document.createElement('DIV');
    e.id = 'ticket-' + id;
    e.classList.add('REG-report-lost-badge');
    return e;
  }

  handleTicket(ticket) {
    var e = this.createCard(ticket.data.id);
    var p = document.getElementById('lost');
    p.append(ticket.display(e, this));
    p.classList.remove('UI-hide');
  }

  processBadges() {
    apiRequest('GET', 'registration/ticket/list', 'max_results=all&picked_up=1')
      .then((response) => {
        var d = JSON.parse(response.responseText);
        if (Array.isArray(d.data)) {
          d.data.forEach((ticket) => {
            this.handleTicket(new LostBadge(ticket));
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

var page = new LostBadgePage();

function load() {
  page.load();
  window.page = page;
}

if (window.addEventListener) {
  window.addEventListener('load', load);
} else {
  window.attachEvent('onload', load);
}
