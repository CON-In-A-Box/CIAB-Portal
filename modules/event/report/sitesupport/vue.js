/* jshint esversion: 6 */
/* globals Vue */

import reportGeneration from '../../../../sitesupport/vue/reportGeneration.js'

var app = Vue.createApp({});
app.component('report-generation', reportGeneration);
app.mount('#page');

export default app;
