/*
 * Javacript for the Art Sales page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals
           */

var artSale = (function(options) {
  'use strict';

  /*
  var configuration;
  */
  var customer;
  var piece;

  var settings = Object.assign(
    {
      debug: true
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    debugmsg: function(message) {
      if (settings.debug) {
        var target = document.getElementById('headline_section');
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },

    load: function() {
    },

    lookupUser: function(origin, item) {
      customer = item;
      console.log(customer);
    },

    lookupPiece: function(origin, item) {
      piece = item;
      console.log(piece);
    }

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', artSale.load);
} else {
  window.attachEvent('onload', artSale.load);
}
