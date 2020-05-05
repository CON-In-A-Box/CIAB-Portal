/*
 * Javacript for the table of Hung art
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals artTable, apiRequest */

var hungArtTable = (function(options) {
  'use strict';

  var fees;

  var settings = Object.assign(
    {
      element: 'artshow_entered',
      onclick: null,
      columns: [{value: 'PieceID', title: 'Piece Number'},
        {value: 'Name', title:'Piece Name'}, 'Medium',
        'Edition', {value:'PieceType', title:'Type'},
        {value: 'NFS', title:'Not<br>For<br>Sale'}, 'Prices',
        {value:'Fee', title:'Hanging Fee'}, 'Status'],
    }, options);

  return {
    options: function(opts) {
      this.parent = new artTable();
      settings = Object.assign(settings, opts);
      this.parent.options(settings);
    },

    load: function(configuration, artist) {
      fees = 0;
      var parent = this.parent;
      return new Promise(function(resolve, reject) {
        var table = parent.buildTable();

        apiRequest('GET', 'artshow/artist/' + artist + '/art',
          'include=id,ArtistID')
          .then(function(response) {
            parent.debugmsg(response.responseText);

            table.appendChild(
              parent.buildTableHeader(configuration));

            parent.pieces = JSON.parse(response.responseText);
            parent.pieces.data.forEach(function(item, index) {
              table.appendChild(
                hungArtTable.buildHungPieceEntry(
                  configuration, item.id, index));
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

    buildHungPieceEntry: function(configuration, data, index) {
      var v;
      if (data.NFS == '1') {
        v = configuration['Artshow_NFSHangingFee'].value;
      } else {
        v = configuration['Artshow_HangingFee'].value;
      }
      fees += parseInt(v);
      return this.parent.buildPieceEntry(configuration, data, index);
    },

    artCount: function() {
      return this.parent.count();
    },

    clear: function() {
      return this.parent.clear();
    },

    hungPiece: function(index) {
      return this.parent.piece(index);
    },

    hangingFees: function() {
      return fees;
    }

  };
})();
