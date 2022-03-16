/* jshint esversion: 6 */
/* globals Vue */

import lookupUser from '../../../sitesupport/lookupuser.js'

var app = Vue.createApp({});
app.component('lookup-user', lookupUser);
app.mount('#concom_lookup');

export default app;
