/*
 * Javacript for the Registration badge checkin
 */

/* jshint browser: true */
/* jshint -W097 */
/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner */

'use strict';

import { RegConfiguration } from './configuration.js';

export class RegPage {
  constructor(options) {
    this.settings = Object.assign(
      {
        'debug': true
      }, options);
    this.isOpen = false;
  }

  processBadges() {}

  load() {
    this.configuration = new RegConfiguration(() => {
      showSpinner();
      apiRequest('GET', 'registration/open', null)
        .then((response) => {
          var result = JSON.parse(response.responseText);
          this.isOpen = result.open;
          this.processBadges();
        })
        .catch((response) => {
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
