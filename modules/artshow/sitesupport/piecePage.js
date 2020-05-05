/*
 * Javacript for the Artshow Piece Details
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, artshowPiece  */

var artshowPiecePage = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      'debug': true,
    }, options);

  var configuration;

  return {

    debugmsg: function(message) {
      if (settings.debug) {
        var target = document.getElementById('headline_section');
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },


    load: function() {
      showSpinner();
      var params = new URLSearchParams(window.location.search);
      if (!params.has('pieceId')) {
        hideSpinner();
      } else {
        apiRequest('GET',
          'artshow/',
          'maxResults=all')
          .then(function(response) {
            var result = JSON.parse(response.responseText);
            configuration = result.data;
            artshowPiece.buildForm(configuration, 'hung_art_form');
            artshowPiece.load(configuration, params.get('pieceId'),
              params.get('eventId'))
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            artshowPiecePage.debugmsg(response.responseText);
          })
          .finally(function() {
            hideSpinner();
          });
      }
    }

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', artshowPiecePage.load);
} else {
  window.attachEvent('onload', artshowPiecePage.load);
}
