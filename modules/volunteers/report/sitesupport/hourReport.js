/* jshint esversion: 6 */
/* globals Vue */

import volunteerReport from './volunteerReport.js'
import reportGeneration from '../../../../sitesupport/reportGeneration.vue.js'

var app = Vue.createApp({});
app.component('volunteer-report', volunteerReport);
app.component('report-generation', reportGeneration);
app.mount('#page');

export default app;
