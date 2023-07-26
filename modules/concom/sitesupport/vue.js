/* jshint esversion: 6 */
/* globals Vue */

import lookupUser from '../../../sitesupport/vue/lookupuser.js'

var app = Vue.createApp({
  methods: {
    onLookup(origin, item) {
      document.getElementById('add_button').disabled = false;
      document.getElementById('user_id').value = item.Id;
    }
  }
});
app.component('lookup-user', lookupUser);
app.mount('#concom_lookup');

export default app;
