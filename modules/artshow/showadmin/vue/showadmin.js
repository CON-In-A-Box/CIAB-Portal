/* jshint esversion: 6 */
/* globals Vue, confirmbox, showSpinner, hideSpinner, apiRequest, alertbox, CIABEventSource,
           progressSpinner */

import lookupPiece from '../../sitesupport/modules/lookupPiece.js'
import artPiece from '../../sitesupport/modules/piece.js';
import lookupUser from '../../../../sitesupport/vue/lookupuser.js';
import findArt from '../../sitesupport/modules/findArt.js';

var app = Vue.createApp({
  mounted() {
    apiRequest('GET',
      'artshow/',
      'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        delete this.configuration.returnmethod['type'];
        delete this.configuration.registrationquestion['type'];
      });
  },
  created() {
    console.log('Loading Artshow Admin');
    this.getStats();
  },
  data() {
    return {
      configuration: {
        Artshow_DisplayArtName: {},
        Artshow_PrintArtName: {},
      },
      debug: false,
      debugmsg: null,
      piece: null,
      reportType: 'json',
      tagPrintCount: 30,
      unprintedCount: 0,
      stats:  {
        artist_count: 0,
        event_artist_count: 0,
        event_hung_count: 0,
        event_print_count: 0,
        event_hung_sale_count: 0,
        event_hung_sale_total: 0,
        event_print_sale_count: 0,
        event_print_sale_total: 0,
        unprinted_tag_count: 0
      },
      data: ''
    }
  },
  methods: {
    getStats() {
      apiRequest('GET', 'artshow/stats')
        .then((response) => {
          this.stats = JSON.parse(response.responseText);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        })
    },
    lookupPiece(lookup, data) {
      this.piece = data;
      if (data != null) {
        this.$refs.pce.loadPiece(this.piece.id);
      } else {
        this.$refs.pce.clear();
      }
    },
    savePiece() {
      confirmbox(
        'Confirm Update of Piece',
        'Are you sure you want to save these changes?')
        .then(() => {
          showSpinner();
          this.$refs.pce.savePiece()
            .then((response) => {
              this.debugmsg = response.responseText;
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugmsg = response.responseText;
            })
            .finally(() => {
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    bidTag() {
      showSpinner();
      const evtSource = new CIABEventSource(
        'api/artshow/art/tag/' + this.piece.id + '?official=true',
        { bufferSizeLimit: -1 });
      evtSource.onmessage = (event) => {
        if (event.lastEventId == 'END') {
          hideSpinner();
          evtSource.close();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        }
      }
    },
    saleReport() {
      showSpinner();
      this.data = '';
      const evtSource = new CIABEventSource('api/artshow/sale');
      evtSource.onmessage = (event) => {
        this.data = this.data + event.data;
        if (event.data == 'END') {
          evtSource.close();
          hideSpinner();
          var file;
          var fileURL;
          if (this.reportType == 'json') {
            file = new Blob([ this.data ],
              { type: 'text/json' });
            fileURL = URL.createObjectURL(file);
            window.open(fileURL);
          } else {
            var data = JSON.parse(this.data);
            var output = 'buyer,piece,id,artist,price_type,price\n';
            for (var d in  data.data) {
              output += data.data[d].buyer.id + ',';
              output += '\'' + data.data[d].piece.name + '\',';
              output += data.data[d].piece.id + ',';
              output += '\'' + this.generateArtistName(data.data[d].piece.artist) + '\',';
              output += '\'' + data.data[d].price_type + '\',';
              output += data.data[d].price;
              output += '\n';
            }
            file = new Blob([ output ],
              { type: 'text/plain' });
            fileURL = URL.createObjectURL(file);
            window.open(fileURL);
          }
        }
      };
    },
    auctionReport() {
      showSpinner();
      this.data = '';
      const evtSource = new CIABEventSource('api/artshow/sale/auction/report');
      evtSource.onmessage = (event) => {
        this.data = this.data + event.data;
        if (event.data == 'END') {
          evtSource.close();
          hideSpinner();
          var file;
          var fileURL;
          if (this.reportType == 'json') {
            file = new Blob([ this.data ],
              { type: 'text/json' });
            fileURL = URL.createObjectURL(file);
            window.open(fileURL);
          } else {
            var data = JSON.parse(this.data);
            var output = 'id,piece,artist,location\n';
            for (var d in  data.data) {
              output += data.data[d].id + ',';
              output += '\'' + data.data[d].name + '\',';
              output += '\'' + this.generateArtistName(data.data[d].artist) + '\',';
              output += '\'' + data.data[d].location + '\'';
              output += '\n';
            }
            file = new Blob([ output ],
              { type: 'text/plain' });
            fileURL = URL.createObjectURL(file);
            window.open(fileURL);
          }
        }
      };
    },
    generateArtistName(artist) {
      if (artist.company_name_on_sheet == '1') {
        return artist.company_name;
      }
      return artist.member.first_name + ' ' + artist.member.last_name;
    },
    printTags() {
      showSpinner(this.tagPrintCount);
      const evtSource = new CIABEventSource(
        'api/artshow/tags?official=true&max_count=' + this.tagPrintCount,
        { bufferSizeLimit: -1 });
      evtSource.onmessage = (event) => {
        if (event.lastEventId == 'END') {
          evtSource.close();
          hideSpinner();
          this.getStats();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        } else {
          progressSpinner(this.tagPrintCount - event.lastEventId);
        }
      };
    },
    lookupUser(lookup, item) {
      showSpinner();
      apiRequest('GET', 'artshow/artist/member/' + item.Id)
        .then((response) => {
          var data = JSON.parse(response.responseText);
          this.artistInvoice(data.id);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          alertbox('No invoice to print');
        })
        .finally(() => {
          hideSpinner();
        });

    },
    artistInvoice(artist) {
      showSpinner();
      apiRequest('GET',
        'artshow/artist/' + artist + '/invoice', '', true)
        .then((response) => {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          alertbox('No invoice to print');
        })
        .finally(() => {
          hideSpinner();
        });
    },
  }
});

app.component('lookup-piece', lookupPiece);
app.component('art-piece', artPiece);
app.component('lookup-user', lookupUser);
app.component('find-art', findArt);

app.mount('#main_content');
