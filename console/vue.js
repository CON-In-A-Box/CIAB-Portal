/* jshint esversion: 6 */
/* globals Vue, switchConsole */

import lookupUser from '../sitesupport/vue/lookupuser.js'

var app = Vue.createApp({
  methods: {
    handleResult(origin, item) {
      var uid = item.Id;
      switchConsole(uid);
    }
  }
});
app.component('lookup-user', lookupUser);
app.mount('#console_dlg');

export default app;
