/*
 * Javacript for the API Configuration
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest */
'use strict';

export class apiConfiguration {
  constructor(resource, resolve, reject) {
    var obj = this;
    apiRequest('GET', resource, 'maxResults=all')
      .then(function(response) {
        var v = JSON.parse(response.responseText).data;
        if (v) {
          v.forEach(function(e) {
            obj[e.field] = e;
          });
        }
        if (resolve) {
          resolve(response);
        }
      })
      .catch(function(response) {
        if (response instanceof Error) { throw response; }
        if (reject) {
          reject(response);
        }
      });
  }
}
