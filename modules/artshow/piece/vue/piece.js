/* jshint esversion: 6 */
/* globals Vue */

import artPiece from '../../sitesupport/modules/piece.js';

var app = Vue.createApp({
  mounted() {
    var params = new URLSearchParams(window.location.search);
    if (params.has('pieceId')) {
      this.$refs.pce.loadPiece(params.get('pieceId'), params.get('eventId'));
    }
  },
  data() {
    return {
    }
  },
  methods: {
  }
});
app.component('art-piece', artPiece);
app.mount('#page');

export default app;
