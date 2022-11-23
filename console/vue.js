/* jshint esversion: 6 */
/* globals Vue */

import lookupUser from '../sitesupport/vue/lookupuser.js'

var app = Vue.createApp({});
app.component('lookup-user', lookupUser);
app.mount('#console_dlg');

export default app;
