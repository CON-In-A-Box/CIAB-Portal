/* jshint esversion: 6 */
/* globals Vue, apiRequest, adminMode */

import lookupUser from '../../../../sitesupport/vue/lookupuser.js'
import hourTable from '../../sitesupport/hourTable.js'
import editHours from './editHours.js'
import editPrize from './editPrize.js'
import checkout from './checkout.js'
import processReturn from './processReturn.js'
import departmentDropdown from '../../../../../sitesupport/vue/departmentDropdown.js'
import giftTable from './giftTable.js'
import hourEntryField from './hourEntryField.js'

var adminHourTable = {
  extends: hourTable,
  methods: {
    clicked(record) {
      if (this.$parent.isAdmin && this.$parent.userId) {
        this.$parent.$refs.edhrs.show(record);
      }
    }
  }
}

var app = Vue.createApp({
  data() {
    return {
      totalHours: 0,
      totalSpentHours: 0,
      isAdmin: false,
      userId: null,
      reward_groups: null,
    }
  },
  created() {
    const searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('volunteerId')) {
      this.userId = searchParams.get('volunteerId');
    }
    this.isAdmin = adminMode;
    this.reloadPrizeGroups();
  },
  methods: {
    handleHourChange(totalHours, totalSpentHours) {
      this.totalHours = parseFloat(totalHours);
      this.totalSpentHours = parseFloat(totalSpentHours);
    },
    printHours(value) {
      var v = parseFloat(value);
      var min = Math.floor((v - Math.floor(v)) * 60);
      var s = (Math.floor(v) != 1) ? 's' : '';
      if (min == 0.0) {
        return Math.floor(v).toLocaleString('en-US') + ' Hour' + s + ' ';
      } else {
        return Math.floor(v).toLocaleString('en-US') + ' Hour' + s + ' ' + min + ' Minutes ';
      }
    },
    reloadPrizeGroups() {
      apiRequest('GET', '/volunteer/reward_group','max_results=all')
        .then((response) => {
          const data = JSON.parse(response.responseText);
          this.reward_groups = data.data;
        })
    }
  }
});

app.component('lookup-user', lookupUser);
app.component('volunteer-hour-table', adminHourTable);
app.component('edit-hours', editHours);
app.component('edit-prize', editPrize);
app.component('process-return', processReturn);
app.component('checkout', checkout);
app.component('department-dropdown', departmentDropdown);
app.component('gift-table', giftTable);
app.component('hour-entry-field', hourEntryField);
app.mount('#main_content');

export default app;
