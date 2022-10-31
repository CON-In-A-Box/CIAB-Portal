/*
 * Javacript for the Registration Configuration
 */

/* jshint browser: true */
/* jshint -W097 */

import { apiConfiguration } from '../../../../sitesupport/apiConfiguration.js';

export class RegConfiguration extends apiConfiguration {
  constructor(resolve, reject) {
    super('registration/configuration', resolve, reject);
  }
}
