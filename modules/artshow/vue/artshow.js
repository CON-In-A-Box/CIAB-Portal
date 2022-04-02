/* jshint esversion: 6 */
/* globals Vue, apiRequest,
   showSidebar, confirmbox, showSpinner, hideSpinner, hideSidebar, CIABEventSource*/

import hungArtTable from '../sitesupport/modules/hungArtTable.js'
import printArtTable from '../sitesupport/modules/printArtTable.js'
import wonArtTable from '../sitesupport/modules/wonArtTable.js'
import artPiece from '../sitesupport/modules/piece.js'
import artPrint from '../sitesupport/modules/print.js'
import printArtEntryTable from '../sitesupport/modules/printArtEntryTable.js'
import hungArtEntryTable from '../sitesupport/modules/hungArtEntryTable.js'
import limitedTextField from '../../../sitesupport/vue/limitedTextField.js'

var app = Vue.createApp({
  mounted() {
    showSpinner();
    apiRequest('GET',
      'artshow/',
      'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        delete this.configuration.returnmethod['type'];
        delete this.configuration.registrationquestion['type'];

        this.selfRegistration = (parseInt(this.configuration['Artshow_SelfRegistration'].value) > 0);
        if (this.selfRegistration) {
          var date = new Date(Date.parse(this.configuration['Artshow_OnlineCloses'].value));
          this.closeDate = date.toDateString();
        } else {
          this.closeDate = null;
        }

        apiRequest('GET', 'artshow/artist', null)
          .then((response) => {
            this.artist = JSON.parse(response.responseText);
            apiRequest('GET', 'artshow/artist/' + this.artist.id + '/show', null)
              .then((response) => {
                this.debugmsg(response.responseText);
                this.show = JSON.parse(response.responseText);
                this.eventRegistered = true;
                if (!this.selfRegistration) {
                  this.$refs.pce.markReadOnly();
                  this.$refs.pnt.markReadOnly();
                }
                this.buildHungArtTables();
                this.buildPrintArtTables();
              })
              .catch((response) => {
                if (response instanceof Error) { throw response; }
                this.$refs.tbl.clear();
                this.$refs.prt_tbl.clear();
              });
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
          });
      })
      .catch((response) => {
        if (response instanceof Error) { throw response; }
      })
      .finally(() => {
        hideSpinner();
      });
    this.$refs.won.loadList('current')
      .then(() => {
        this.wonCount = this.$refs.won.count();
        this.wonCost = this.$refs.won.getCost();
        this.wonOwed = this.$refs.won.getCost() - this.paid;
      })
      .catch((response) => {
        if (response instanceof Error) { throw response; }
      });
    apiRequest('GET', 'artshow/customer/current/payment', null)
      .then((response) => {
        var data = JSON.parse(response.responseText);
        this.payments = data.data;
        for (var i in this.payments) {
          this.paid += parseFloat(data.data[i].amount);
        }
        this.wonOwed -= this.paid;
      })
      .catch((response) => {
        if (response instanceof Error) { throw response; }
      });
  },
  data() {
    return {
      configuration: {
        event: {
          name: 'Event'
        },
        Artshow_PrintShop: 0,
        Artshow_DisplayArtName: {},
        Artshow_PrintArtName: {},
        Artshow_MailInAllowed: {},
      },
      closeDate: null,
      selfRegistration: false,
      eventRegistered: false,
      artist: null,
      show: null,
      artSold: 0,
      artSoldValue: 0,
      printSold: 0,
      printSoldValue: 0,
      itemsSold: 0,
      soldValue: 0,
      hangingFee: 0,
      editPieceID: null,
      debug: false,
      debugElement: 'headline_section',
      wonCount: 0,
      wonCost: 0,
      wonOwed: 0,
      hungCount: 0,
      printCount: 0,
      artCommission: 0,
      printCommission: 0,
      commission: 0,
      netIncome: 0,
      totalArtCount: 0,
      payments: null,
      paid: 0
    }
  },
  methods: {
    debugmsg(message) {
      if (this.debug && this.debugElement) {
        var target = document.getElementById(this.debugElement);
        if (target) {
          target.classList.add('UI-show');
          target.innerHTML = message;
        }
      }
    },
    updateArtSold(count, value) {
      this.artSold = parseInt(count);
      this.artSoldValue = parseInt(value);
      this.itemsSold = this.artSold + this.printSold;
      this.soldValue = this.artSoldValue + this.printSoldValue;
      this.netIncome = (this.soldValue - this.hangingFee - this.commission).toFixed(2);
    },
    updatePrintSold(count, value) {
      this.printSold = parseInt(count);
      this.printSoldValue = parseInt(value);
      this.itemsSold = this.artSold + this.printSold;
      this.soldValue = this.artSoldValue + this.printSoldValue;
      this.netIncome = (this.soldValue - this.hangingFee - this.commission).toFixed(2);
    },
    updateArtCommission(value) {
      this.artCommission = value;
      this.commission = (this.printCommission + this.artCommission).toFixed(2);
      this.netIncome = (this.soldValue - this.hangingFee - this.commission).toFixed(2);
    },
    updatePrintCommission(value) {
      this.printCommission = value;
      this.commission = (this.printCommission + this.artCommission).toFixed(2);
      this.netIncome = (this.soldValue - this.hangingFee - this.commission).toFixed(2);
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
    artistHungTags() {
      showSpinner();
      const evtSource = new CIABEventSource(
        'api/artshow/artist/' + this.artist.id + '/tags',
        {getArgs:{ 'evs_buffer_size_limit': -1}});
      evtSource.onmessage = (event) => {
        if (event.lastEventId == 'END') {
          evtSource.close();
          hideSpinner();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        }
      }
    },
    addPrintArt() {
      this.$refs.prt_entry_tbl.load(this.artist.id);
      document.getElementById('enter_printshop').style.display = 'block';
    },
    editPiece(data) {
      this.$refs.pce.displayPiece(data, null);
      this.editPieceID = data.id;
      showSidebar('hung_art');
    },
    deletePiece() {
      confirmbox(
        'Confirm Removal of Piece',
        'Are you sure you want to permenantly delete this Piece?')
        .then(() => {
          showSpinner();
          apiRequest('DELETE', 'artshow/art/' + this.editPieceID, null)
            .finally(() => {
              this.buildHungArtTables();
              hideSidebar();
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    savePiece() {
      confirmbox(
        'Confirm Update of Piece',
        'Are you sure you want to save these changes?')
        .then(() => {
          showSpinner();
          this.$refs.pce.savePiece()
            .then((response) => {
              this.debugmsg(response.responseText);
              this.buildHungArtTables();
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugmsg(response.responseText);
            })
            .finally(() => {
              hideSidebar();
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    pieceBidTag() {
      showSpinner();
      const evtSource = new CIABEventSource('api/artshow/art/tag/' + this.editPieceID);
      evtSource.onmessage = (event) => {
        if (event.lastEventId == 'END') {
          evtSource.close();
          hideSpinner();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        }
      }
    },
    savePrint() {
      confirmbox(
        'Confirm Update of ' + this.configuration.Artshow_PrintArtName.value + ' Lot',
        'Are you sure you want to save these changes?')
        .then(() => {
          showSpinner();
          this.$refs.pnt.savePiece()
            .then((response) => {
              this.debugmsg(response.responseText);
              this.buildPrintArtTables();
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugmsg(response.responseText);
            })
            .finally(() => {
              hideSidebar();
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    deletePrint() {
      confirmbox(
        'Confirm Removal of ' + this.configuration.Artshow_PrintArtName.value + ' Lot',
        'Are you sure you want to permenantly delete this ' + this.configuration.Artshow_PrintArtName.value + ' lot?')
        .then(() => {
          showSpinner();
          apiRequest('DELETE', 'artshow/print/' + this.editPieceID, null)
            .finally(() => {
              this.buildPrintArtTables();
              hideSidebar();
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    closeHungArt() {
      document.getElementById('enter_hung').style.display = 'none';
    },
    closePrintArt() {
      document.getElementById('enter_printshop').style.display = 'none';
    },
    closeBidSheet() {
      document.getElementById('bidsheet').style.display = 'none';
    },
    submitHungArt() {
      showSpinner();
      this.$refs.hung_entry_tbl.submitArt()
        .then(() => {
          this.buildHungArtTables();
          this.closeHungArt();
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        })
        .finally(() => {
          hideSpinner();
        });
    },
    submitPrintArt() {
      showSpinner();
      this.$refs.prt_entry_tbl.submitArt()
        .then(() => {
          this.buildPrintArtTables();
          this.closePrintArt();
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        })
        .finally(() => {
          hideSpinner();
        });
    },
    editPrint(data) {
      this.editPieceID = data.id;
      this.$refs.pnt.displayArt(data, null);
      showSidebar('shop_art');
    },
    eventShowRegister() {
      this.show = {
        mail_in: '0'
      };
      for (var x in this.configuration.registrationquestion) {
        if (x == 'type') {
          continue;
        }
        this.show['custom_question_' + this.configuration.registrationquestion[x].id] = '';
      }
    },
    eventRegister() {
      var method = 'POST';
      if (this.show.event) {
        method = 'PUT';
      }
      showSpinner();
      var params = [];
      for (const [key, value] of Object.entries(this.show)) {
        if (key != 'event' && key != 'artist' && key != 'type' && value != null) {
          params.push(`${key}=${value}`);
        }
      }
      var query = params.join('&');
      apiRequest(method, 'artshow/artist/' + this.artist.id + '/show', query)
        .then((response) => {
          this.debugmsg(response.responseText);
          this.show = JSON.parse(response.responseText);
          this.buildHungArtTables();
          this.buildPrintArtTables();
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugmsg(response.responseText);
        })
        .finally(() => {
          hideSpinner();
        });
    },
    artistRegister() {
      showSpinner();
      apiRequest('POST', 'artshow/artist')
        .then((response) => {
          this.debugmsg(response.responseText);
          this.artist = JSON.parse(response.responseText);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugmsg(response.responseText);
        })
        .finally(() => {
          hideSpinner();
        });
    },
    artistUpdateRegister() {
      showSpinner();
      apiRequest('PUT', 'artshow/artist/' + this.artist.id,
        'company_name=' + this.artist.company_name +
       '&company_name_on_sheet=' + this.artist.company_name_on_sheet +
       '&company_name_on_payment=' + this.artist.company_name_on_payment +
       '&website=' + this.artist.website +
       '&professional=' + this.artist.professional
      )
        .then((response) => {
          this.debugmsg(response.responseText);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugmsg(response.responseText);
        })
        .finally(() => {
          hideSpinner();
        });
    },
    buildPrintArtTables() {
      showSpinner();
      this.$refs.prt_tbl.loadArtist(this.artist.id)
        .then(() => {
          this.artCount();
          this.printCount = this.$refs.prt_tbl.count();
        })
        .catch((response) => {
          if (response) {
            if (response instanceof Error) { throw response; }
            this.debugmsg(response.responseText);
          }
          this.$refs.prt_tbl.clear();
          this.artCount();
          this.printCount = 0;
        })
        .finally(() => {
          hideSpinner();
        });
    },
    buildHungArtTables() {
      showSpinner();
      this.$refs.tbl.loadArtist(this.artist.id)
        .then(() => {
          this.artCount();
          this.hungCount = this.$refs.tbl.count();
        })
        .catch((response) => {
          if (response) {
            if (response instanceof Error) { throw response; }
            this.debugmsg(response.responseText);
          }
          this.$refs.tbl.clear();
          this.artCount();
          this.hungCount = 0;
        })
        .finally(() => {
          hideSpinner();
        });
    },
    artCount() {
      var c = 0;
      c += this.$refs.tbl.count();
      c += this.$refs.prt_tbl.count();
      this.totalArtCount = c;
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
    },
  }
});

app.component('hung-art-table', hungArtTable);
app.component('print-art-table', printArtTable);
app.component('won-art-table', wonArtTable);
app.component('art-piece', artPiece);
app.component('art-print', artPrint);
app.component('print-art-entry-table', printArtEntryTable);
app.component('hung-art-entry-table', hungArtEntryTable);
app.component('limited-text-field', limitedTextField);

app.mount('#page');

window.vue_table = app;

export default app;
