/* jshint esversion: 6 */
/* globals Vue, adminMode */

import lookupUser from '../../../../sitesupport/vue/lookupuser.js'
import hourTable from '../../sitesupport/hourTable.js'
import editHours from './editHours.js'
import departmentDropdown from '../../../../../sitesupport/vue/departmentDropdown.js'


var adminHourTable = {
  extends: hourTable,
  methods: {
    clicked(record) {
      if (adminMode && this.user) {
        this.$parent.$refs.edhrs.show(record);
      }
    }
  }
}

var app = Vue.createApp({});
app.component('lookup-user', lookupUser);
app.component('volunteer-hour-table', adminHourTable);
app.component('edit-hours', editHours);
app.component('department-dropdown', departmentDropdown);
app.mount('#main_content');

export default app;
