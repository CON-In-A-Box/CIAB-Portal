/* jshint esversion: 6 */
/* globals Vue, apiRequest, showSpinner, hideSpinner, userProfile,
           showSidebar, hideSidebar, confirmbox, progressSpinner,
           systemDebug, CIABEventSource */


import lookupUser from '../../../../sitesupport/vue/lookupuser.js';
import hungArtTable from '../../sitesupport/modules/hungArtTable.js';
import hungArtEntryTable from '../../sitesupport/modules/hungArtEntryTable.js';
import printArtTable from '../../sitesupport/modules/printArtTable.js';
import artPiece from '../../sitesupport/modules/piece.js';
import artPrint from '../../sitesupport/modules/print.js';
import printArtEntryTable from '../../sitesupport/modules/printArtEntryTable.js'
import limitedTextField from '../../../../sitesupport/vue/limitedTextField.js'

var app = Vue.createApp({
  mounted() {
    showSpinner();
    apiRequest('GET',
      'artshow',
      'max_results=all')
      .then((response) => {
        var params = new URLSearchParams(window.location.search);
        this.configuration = JSON.parse(response.responseText);
        this.debugMessage = response.responseText;
        if (params.has('artistId')) {
          var artistId = params.get('artistId');
          apiRequest('GET', 'artshow/artist/' + artistId, null)
            .then((response) => {
              this.debugMessage = response.responseText;
              this.loadArtist(JSON.parse(response.responseText))
                .finally(()  => {
                  hideSpinner();
                });
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugMessage = response.responseText;
              hideSpinner();
            })
        } else {
          hideSpinner();
        }
      })
      .catch((response) => {
        if (response instanceof Error) { throw response; }
        this.debugMessage = response.responseText;
      });
  },
  data() {
    return {
      debug: systemDebug,
      debugMessage: null,
      configuration: {
        Artshow_DisplayArtName: {},
        Artshow_PrintArtName: {},
      },
      artist: {},
      editPieceID : null,
    }
  },
  methods: {
    clear() {
      this.artst = {};
      userProfile.clear();
      document.getElementsByName('art_button').forEach((e) => {
        e.disabled = true;
      });
      this.$refs.tbl.clear();
      this.$refs.prnt.clear();
    },

    lookup(lookup, item) {
      showSpinner();
      apiRequest('GET', 'artshow/artist/member/' + item.Id, null)
        .then((response) => {
          this.loadArtist(JSON.parse(response.responseText))
            .finally(() => {
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugMessage = response.responseText;
          hideSpinner();
          this.clear();
        })
    },

    loadArtist(data) {
      var promises = Array();
      this.artist = data;
      userProfile.populate(data.member);
      promises.push(this.$refs.tbl.loadArtist(this.artist.id));
      promises.push(this.$refs.prnt.loadArtist(this.artist.id));
      document.getElementsByName('art_button').forEach((e) => {
        e.disabled = false;
      });
      return Promise.all(promises);
    },

    artistProfile() {
      window.location = 'index.php?Function=artshow/artist&accountId=' +
        userProfile.accountId;
    },

    editPiece(data) {
      this.editPieceID = data.id;
      this.$refs.pce.displayPiece(data, null);
      showSidebar('hung_art');
      this.$refs.pce.scalePiece();
    },

    savePiece() {
      confirmbox(
        'Confirm Update of Piece',
        'Are you sure you want to save these changes?')
        .then(() => {
          showSpinner();
          this.$refs.pce.savePiece()
            .then((response) => {
              this.debugMessage = response.responseText;
              this.$refs.tbl.loadArtist(this.artist.id);
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugMessage = response.responseText;
            })
            .finally(() => {
              hideSidebar();
              hideSpinner();
            });
        });
    },

    deletePiece() {
      confirmbox(
        'Confirm Removal of Piece',
        'Are you sure you want to permenantly delete this Piece?')
        .then(() => {
          showSpinner();
          apiRequest('DELETE', 'artshow/art/' + this.editPieceID, null)
            .finally(() => {
              this.$refs.tbl.loadArtist(this.artist.id);
              hideSidebar();
              hideSpinner();
            });
        });
    },

    markAsHung(pieces) {
      var loc = document.getElementById('location').value;
      if (!loc) {
        loc = 'hung';
      }
      for (var index in pieces) {
        var piece = pieces[index];
        if (piece.location != loc) {
          var param = 'location=' + loc;
          apiRequest('PUT', 'artshow/art/' + piece.id, param)
            .then(() => {this.$refs.tbl.loadArtist(this.artist.id);});
        }
      }
    },

    editPrint(data) {
      this.editPieceID = data.id;
      this.$refs.prt.displayArt(data, null);
      showSidebar('shop_art');
      this.$refs.prt.scalePiece();
    },

    savePrint() {
      confirmbox(
        'Confirm Update of ' + this.configuration.Artshow_PrintArtName.value + ' Lot',
        'Are you sure you want to save these changes?')
        .then(() => {
          showSpinner();
          this.$refs.prt.savePiece()
            .then((response) => {
              this.debugMessage = response.responseText;
              this.$refs.prnt.loadArtist(this.artist.id);
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugMessage = response.responseText;
            })
            .finally(() => {
              hideSidebar();
              hideSpinner();
            });
        });
    },

    deletePrint() {
      confirmbox(
        'Confirm Removal of ' + this.configuration.Artshow_PrintArtName.value + 'Lot',
        'Are you sure you want to permenantly delete this Lot of ' + this.configuration.Artshow_PrintArtName.value + '?')
        .then(() => {
          showSpinner();
          apiRequest('DELETE', 'artshow/print/' + this.editPieceID, null)
            .finally(() => {
              this.$refs.prnt.loadArtist(this.artist.id);
              hideSidebar();
              hideSpinner();
            });
        });
    },

    addHungArt() {
      var max = parseInt(this.configuration['Artshow_DisplayLimit'].value);
      if (max < 1 || max > 20) {max = 20;}
      if (this.$refs.tbl.count()) {
        max -= this.$refs.tbl.count();
      }
      this.$refs.hung_entry_tbl.load(this.artist.id, max);
      document.getElementById('enter_hung').style.display = 'block';
    },

    closeHungArt() {
      document.getElementById('enter_hung').style.display = 'none';
    },

    submitHungArt() {
      showSpinner();
      this.$refs.hung_entry_tbl.submitArt()
        .then(() => {
          this.$refs.tbl.loadArtist(this.artist.id);
          this.closeHungArt();
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugMessage = response.responseText;
        })
        .finally(() => {
          hideSpinner();
        });
    },

    addPrintArt() {
      this.$refs.prt_entry_tbl.artist = this.artist.id;
      document.getElementById('enter_printshop').style.display = 'block';
    },

    closePrintArt() {
      document.getElementById('enter_printshop').style.display = 'none';
    },

    submitPrintArt() {
      showSpinner();
      this.$refs.prt_entry_tbl.submitArt()
        .then(() => {
          this.$refs.prnt.loadArtist(this.artist.id);
          this.closePrintArt();
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugMessage = response.responseText;
        })
        .finally(() => {
          hideSpinner();
        });
    },

    pieceBidTag() {
      const evtSource = new CIABEventSource(
        'api/artshow/art/tag/' + this.editPieceID + '?official=true',
        { bufferSizeLimit: -1 });
      evtSource.onmessage = (event) => {
        if (event.lastEventId == 'END') {
          evtSource.close();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        }
      }
    },

    tagHungArt() {
      showSpinner();
      var i = 0;
      const evtSource = new CIABEventSource(
        'api/artshow/artist/' + this.artist.id + '/tags?official=true',
        { bufferSizeLimit: -1 });
      evtSource.onmessage = (event) => {
        if (event.lastEventId > i) {
          hideSpinner();
          showSpinner(event.lastEventId);
          i = parseInt(event.lastEventId);
        }
        if (event.lastEventId == 'END') {
          evtSource.close();
          hideSpinner();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          var w = window.open(fileURL);
          w.print();
        } else {
          progressSpinner(i - event.lastEventId);
        }
      };
    },
    generateInventory() {
      apiRequest('GET',
        'artshow/artist/' + this.artist.id + '/inventory',
        '', true)
        .then((response) => {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        })
    }
  }
});


app.component('lookup-user', lookupUser);
app.component('hung-art-table', hungArtTable);
app.component('print-art-table', printArtTable);
app.component('art-piece', artPiece);
app.component('art-print', artPrint);
app.component('print-art-entry-table', printArtEntryTable);
app.component('hung-art-entry-table', hungArtEntryTable);
app.component('limited-text-field', limitedTextField);
app.mount('#page');

window.vue_table = app;

export default app;
