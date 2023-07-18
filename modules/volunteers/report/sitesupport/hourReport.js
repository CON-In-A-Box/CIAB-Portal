/* jshint esversion: 6 */
/* globals Vue, showSidebar */

import volunteerReport from './volunteerReport.js'
import hourTable from '../../sitesupport/hourTable.js'
import reportGeneration from '../../../../sitesupport/vue/reportGeneration.js'

var reportHourTable = {
  extends: hourTable,
  methods: {
    clicked(department) {
      showSidebar('department_report_div');
      document.getElementById('dept_name').innerHTML = department['name'];
      document.getElementById('dept_data').value = department['id'];
      document.getElementById('dept_data_name').value = department['name'];
    }
  }
}

var app = Vue.createApp({});
app.component('volunteer-report', volunteerReport);
app.component('report-generation', reportGeneration);
app.component('volunteer-hour-table', reportHourTable);
app.mount('#page');

export default app;
