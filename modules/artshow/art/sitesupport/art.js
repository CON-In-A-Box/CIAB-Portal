/*
 * Javacript for the Artist Art page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, userLookup, userProfile,
           hungArtTable, printArtTable, artshowPiece, showSidebar,
           artshowPrint, hideSidebar, confirmbox, hungArtEntryTable,
           printArtEntryTable */

var artPage = (function(options) {
  'use strict';

  var configuration;
  var artistId;
  var editPieceID;

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

    clear: function() {
      artistId = -1;
      userProfile.clear();
      document.getElementById('company').value = '';
      document.getElementById('artist_profile').disabled = true;
      document.getElementsByName('art_button').forEach(function(e) {
        e.disabled = true;
      });
      hungArtTable.clear();
      printArtTable.clear();
    },

    lookup: function(origin, item) {
      document.getElementById('userLookup_dropdown').classList.add('UI-hide');
      setTimeout(function() {
        userLookup.clear();
      }, 3000);
      showSpinner();
      apiRequest('GET', 'artshow/artist/member/' + item.Id,
        'include=AccountID')
        .then(function(response) {
          artPage.loadArtist(JSON.parse(response.responseText))
            .then(function() {
              hideSpinner();
            });
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artPage.debugmsg(response.responseText);
          hideSpinner();
          artPage.clear();
        })
    },

    load: function() {
      document.getElementById('artist_profile').disabled = true;
      showSpinner();
      apiRequest('GET',
        'artshow/',
        'maxResults=all')
        .then(function(response) {
          var params = new URLSearchParams(window.location.search);
          var result = JSON.parse(response.responseText);
          configuration = result.data;
          artPage.debugmsg(response.responseText);
          if (params.has('artistId')) {
            var artistId = params.get('artistId');
            apiRequest('GET', 'artshow/artist/' + artistId,
              'include=AccountID')
              .then(function(response) {
                artPage.debugmsg(response.responseText);
                artPage.loadArtist(JSON.parse(response.responseText))
                  .then(function() {
                    hideSpinner();
                  });
              })
              .catch(function(response) {
                if (response instanceof Error) { throw response; }
                artPage.debugmsg(response.responseText);
                hideSpinner();
              })
          } else {
            hideSpinner();
          }
          artshowPiece.buildForm(configuration, 'hung_art_form');
          artshowPrint.buildForm(configuration, 'shop_art_form');
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artPage.debugmsg(response.responseText);
        });
    },

    loadArtist: function(data) {
      var promises = Array();
      artistId = data.ArtistID;
      userProfile.populate(data.AccountID);
      document.getElementById('company').value = data.CompanyName;
      promises.push(hungArtTable.load(configuration, artistId));
      promises.push(printArtTable.load(configuration, artistId));
      document.getElementById('artist_profile').disabled = false;
      document.getElementsByName('art_button').forEach(function(e) {
        e.disabled = false;
      });
      return Promise.all(promises);
    },

    artistProfile: function() {
      window.location = 'index.php?Function=artshow/artist&accountId=' +
        userProfile.accountId;
    },

    editPiece: function(index) {
      var data = hungArtTable.hungPiece(index).id;
      editPieceID = data.PieceID;
      artshowPiece.displayArt(data);
      showSidebar('hung_art');
      artshowPiece.scale();
    },

    savePiece: function() {
      confirmbox(
        'Confirm Update of Piece',
        'Are you sure you want to save these changes?')
        .then(function() {
          showSpinner();
          artshowPiece.savePiece()
            .then(function(response) {
              artPage.debugmsg(response.responseText);
              hungArtTable.load(configuration, artistId);
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              artPage.debugmsg(response.responseText);
            })
            .finally(function() {
              hideSidebar();
              hideSpinner();
            });
        });
    },

    deletePiece: function() {
      confirmbox(
        'Confirm Removal of Piece',
        'Are you sure you want to permenantly delete this Piece?')
        .then(function() {
          showSpinner();
          apiRequest('DELETE', 'artshow/art/' + editPieceID, null)
            .finally(function() {
              hungArtTable.load(configuration, artistId);
              hideSidebar();
              hideSpinner();
            });
        });
    },

    editPrint: function(index) {
      var data = printArtTable.printPiece(index).id;
      editPieceID = data.PieceID;
      artshowPrint.displayArt(data);
      showSidebar('shop_art');
      artshowPiece.scale();
    },

    savePrint: function() {
      confirmbox(
        'Confirm Update of Print Art Lot',
        'Are you sure you want to save these changes?')
        .then(function() {
          showSpinner();
          artshowPrint.savePiece()
            .then(function(response) {
              artPage.debugmsg(response.responseText);
              printArtTable.load(configuration, artistId);
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              artPage.debugmsg(response.responseText);
            })
            .finally(function() {
              hideSidebar();
              hideSpinner();
            });
        });
    },

    deletePrint: function() {
      confirmbox(
        'Confirm Removal of Print Art Lot',
        'Are you sure you want to permenantly delete this Lot of Print Art?')
        .then(function() {
          showSpinner();
          apiRequest('DELETE', 'artshow/print/' + editPieceID, null)
            .finally(function() {
              printArtTable.load(configuration, artistId);
              hideSidebar();
              hideSpinner();
            });
        });
    },

    addHungArt: function() {
      var max = parseInt(configuration['Artshow_DisplayLimit'].value);
      if (max < 1 || max > 20) {max = 20;}
      if (hungArtTable.artCount()) {
        max -= hungArtTable.artCount();
      }
      hungArtEntryTable.load(configuration, artistId, max);
      document.getElementById('enter_hung').style.display = 'block';
    },

    closeHungArt: function() {
      document.getElementById('enter_hung').style.display = 'none';
    },

    submitHungArt: function() {
      showSpinner();
      hungArtEntryTable.submitArt()
        .then(function() {
          hungArtTable.load(configuration, artistId);
          artPage.closeHungArt();
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artPage.debugmsg(response.responseText);
        })
        .finally(function() {
          hideSpinner();
        });
    },

    addPrintArt: function() {
      printArtEntryTable.load(configuration, artistId);
      document.getElementById('enter_printshop').style.display = 'block';
    },

    closePrintArt: function() {
      document.getElementById('enter_printshop').style.display = 'none';
    },

    submitPrintArt: function() {
      showSpinner();
      printArtEntryTable.submitArt()
        .then(function() {
          printArtTable.load(configuration, artistId);
          artPage.closePrintArt();
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artPage.debugmsg(response.responseText);
        })
        .finally(function() {
          hideSpinner();
        });
    },

    pieceBidTag: function() {
      apiRequest('GET',
        'artshow/art/tag/' + editPieceID,
        'official=true', true)
        .then(function(response) {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        });
    },

    tagHungArt: function() {
      apiRequest('GET',
        'artshow/artist/' + artistId + '/tags',
        'official=true', true)
        .then(function(response) {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          var w = window.open(fileURL);
          w.print();
        });
    },

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', artPage.load);
} else {
  window.attachEvent('onload', artPage.load);
}
