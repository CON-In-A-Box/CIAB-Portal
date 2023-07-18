/* jshint esversion: 6 */
/* globals Vue, showSidebar, adminMode */

import lookupUser from '../../../../sitesupport/vue/lookupuser.js'
import hourTable from '../../sitesupport/hourTable.js'

var adminHourTable = {
  extends: hourTable,
  methods: {
    clicked(record) {
      if (adminMode && this.user) {
        showSidebar('edit_user_hour_div');
        document.getElementById('edit_name').value = record.member.first_name;
        document.getElementById('edit_hours').value = record.hours;
        var options = document.getElementById('edit_mod');
        var value = parseFloat(record.modifier);
        options.selectedIndex = 0;
        for (var i = 0, n = options.length; i < n ; i++) {
          if (options[i].value == value) {
            options.selectedIndex = i;
            break;
          } else if (options[i].value < value) {
            options.selectedIndex = i;
          } else {
            break;
          }
        }
        document.getElementById('edit_enter').value = record.enterer.id;
        document.getElementById('edit_auth').value = record.authorizer.id;
        document.getElementById('edit_dept').value = record.department.name;
        var date = record.end;
        date = date.replace(/\s+/g, 'T');
        document.getElementById('edit_end').value = date;
        document.getElementById('edit_data').value = btoa(JSON.stringify(record));
      }
    }
  }
}

var app = Vue.createApp({});
app.component('lookup-user', lookupUser);
app.component('volunteer-hour-table', adminHourTable);
app.mount('#main_content');

export default app;
