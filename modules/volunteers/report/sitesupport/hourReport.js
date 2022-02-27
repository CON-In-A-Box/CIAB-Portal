/* jshint esversion: 6 */
/* globals Vue */

import volunteerReport from './volunteerReport.js'

var app = Vue.createApp({});

app.component('volunteer-report', volunteerReport);

app.mount('#hour_report_div');
export default app;
