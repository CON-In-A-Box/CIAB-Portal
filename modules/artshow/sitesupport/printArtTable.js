/*
 * Javacript for the Print Art Table
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals artTable, apiRequest */
/* exported printArtTable */

var printArtTable = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      element: 'artshow_printshop',
      onclick: null,
      columns: [{value:'PieceID', title:'Lot Number'}, 'Name',
        {value:'PieceType', title:'Type'}, 'Quantity', 'Price', 'Status'],
    }, options);

  return {
    options: function(opts) {
      this.parent = new artTable();
      settings = Object.assign(settings, opts);
      this.parent.options(settings);
    },

    debugmsg: function(message) {
      this.parent.debugmsg(message);
    },

    load: function(configuration, artist) {
      var parent = this.parent;
      return new Promise(function(resolve, reject) {
        var table = parent.buildTable();

        apiRequest('GET', 'artshow/artist/' + artist + '/print',
          'include=id,ArtistID')
          .then(function(response) {
            table.appendChild(parent.buildTableHeader());

            parent.debugmsg(response.responseText);

            parent.pieces = JSON.parse(response.responseText);
            parent.pieces.data.forEach(function(item, index) {
              table.appendChild(parent.buildPieceEntry(null, item.id, index));
            });
            resolve();
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            parent.debugmsg(response.responseText);
            reject();
          });
      });
    },

    artCount: function() {
      return this.parent.count();
    },

    clear: function() {
      return this.parent.clear();
    },

    printPiece: function(index) {
      return this.parent.piece(index);
    }

  };

})();
