/* jshint esversion: 6 */
/* globals Vue, apiRequest, showSpinner, hideSpinner, userProfile, systemDebug */

import lookupUser from '../../../../sitesupport/vue/lookupuser.js'
import limitedTextField from '../../../../sitesupport/vue/limitedTextField.js'

var app = Vue.createApp({
  mounted() {
  },
  created() {
    console.log('Loading Artshow Artist');
    var params = new URLSearchParams(window.location.search);
    if (params.has('accountId')) {
      var accountId = params.get('accountId');
    }
    apiRequest('GET',
      'artshow',
      'max_results=all')
      .then((response)  => {
        this.debugMessage = response.responseText;
        this.configuration = JSON.parse(response.responseText);
        delete this.configuration.returnmethod['type'];
        delete this.configuration.registrationquestion['type'];

        this.loadData(null, accountId);
      });

  },
  data() {
    return {
      artists: {},
      stats: {},
      configuration: {
        returnmethod: {},
        registrationquestion: {},
        Artshow_MailInAllowed: {},
        Artshow_ComanyName_Charlimit: 0,
        Artshow_Website_Charlimit: 0
      },
      debug: systemDebug,
      debugMessage: null,
      selectedArtist: null,
      artist: {},
      show: {},
      payments: {},
      modified: false,
      distributionAmount: null,
      distributionCheck: null
    }
  },
  methods: {
    generateArtistName(artist) {
      if (artist.company_name_on_sheet == '1') {
        return artist.company_name;
      }
      return artist.member.first_name + ' ' + artist.member.last_name;
    },

    loadData(selectedIndex, accountId) {
      showSpinner();

      apiRequest('GET',
        'artshow/artists',
        'max_results=all')
        .then((response) => {
          this.artists = JSON.parse(response.responseText).data;
          if (selectedIndex != null) {
            this.selectedArtist = selectedIndex;
            this.artist = this.artists[selectedIndex];
            this.payments = {};
            this.selectArtist();
          } else if (accountId != null) {
            for (var i in this.artists) {
              if (this.artists[i].member.id == accountId) {
                this.selectedArtist = i;
                this.artist = this.artists[i];
                this.payments = {};
                this.selectArtist();
              }
            }
          }
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.artists = null;
          this.debugMessage = response.responseText;
        })
        .finally(() => {
          hideSpinner();
        })
    },

    selectArtist(artist) {
      if (typeof artist == 'undefined' || artist.type != 'artist') {
        artist = this.artists[this.selectedArtist];
        this.artist = this.artists[this.selectedArtist];
      }

      this.payments = {};
      this.newArtist(false);

      userProfile.populate(artist.member);

      showSpinner();
      apiRequest('GET', 'artshow/artist/' + artist.id + '/show',
        null)
        .then((response) => {
          this.show = JSON.parse(response.responseText);
          this.debugMessage = response.responseText;
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugMessage = response.responseText;
        })
        .finally(() => {
          hideSpinner();
        });

      apiRequest('GET', 'artshow/artist/' + artist.id + '/sales',
        null)
        .then((response) => {
          this.stats = JSON.parse(response.responseText);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },

    newArtist(clearIndex) {
      userProfile.clear();
      if (clearIndex) {
        this.selectedArtist = null;
        this.artist = {};
        this.stats = {};
        this.payments = {};
      }
      this.show = {};
      this.modified = false;
    },

    artistDataChange() {
      if (userProfile.getElementById('email1').value != '' &&
            (userProfile.getElementById('firstName').value  != '' ||
             userProfile.getElementById('lastName').value != ''))
      {
        this.modified = true;
      } else {
        this.modified = false;
      }
    },

    saveArtistDetails(i, aid) {
      var method = 'POST';
      var uri = 'artshow/artist';
      var body = [ 'id=' + aid ];
      if (i >= 0) {
        method = 'PUT';
        uri = 'artshow/artist/' + this.artists[i].id;
        body = [];
      }
      if (this.artist.company_name) {
        body.push('company_name=' + this.artist.company_name);
      }
      if (this.artist.website) {
        body.push('website=' + this.artist.website);
      }
      body.push('company_name_on_sheet=' + this.artist.company_name_on_sheet);
      body.push('company_name_on_payment=' + this.artist.company_name_on_payment);
      body.push('professional=' + this.artist.professional);
      apiRequest(method, uri, body.join('&'))
        .then((response) => {
          body = [];
          var artist = JSON.parse(response.responseText);
          body.push('mail_in=' + this.show.mail_in);
          if (this.show.return_method) {
            body.push('return_method=' + this.show.return_method);
          }

          for (var idx in this.configuration.registrationquestion) {
            var id = this.configuration.registrationquestion[idx].id;
            var name = 'custom_question_' + id;
            if (name in this.show) {
              var answer = this.show[name];
              body.push(name + '=' + answer);
            } else {
              body.push(name + '=');
            }
          }

          if (this.show.event) {
            method = 'PUT';
          } else {
            method = 'POST';
          }

          apiRequest(method, 'artshow/artist/' + artist.id +
            '/show',
          body.join('&'))
            .then(() => {
              this.loadData(i);
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
            })
            .finally(() =>{
              hideSpinner();
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          hideSpinner();
        });
    },

    saveArtist() {
      var aid;
      showSpinner();
      var data = userProfile.serializeUpdate();
      if (data.length !== 0) {
        userProfile.updateAccount()
          .then((response) => {
            var data = JSON.parse(response.responseText);
            var aid = data.id;
            this.saveArtistDetails(this.selectedArtist, aid);
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            hideSpinner();
          });
      } else {
        aid = userProfile.getElementById('badgeNumber').value;
        this.saveArtistDetails(this.selectedArtist, aid);
      }
    },

    lookup(lookup, item) {
      this.newArtist(true);
      var found = null;
      this.artists.forEach((artist, index) => {
        if (found == null &&
            parseInt(artist.member.id) == parseInt(item.Id)) {
          this.selectedArtist = index;
          found = index;
        }
      });
      if (found !== null) {
        this.selectArtist(this.artists[found]);
      } else {
        showSpinner();
        apiRequest('GET', 'member/' + item.Id, null)
          .then((response) => {
            hideSpinner();
            var data = JSON.parse(response.responseText);
            userProfile.populate(data);
            this.modified = false;
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.debugMessage = response.responseText;
            hideSpinner();
          });
      }
    },

    artistArt() {
      window.location = 'index.php?Function=artshow/art&artistId=' +
        this.artist.id;
    },

    artistInvoice() {
      showSpinner();
      apiRequest('GET',
        'artshow/artist/' + this.artist.id + '/invoice', '', true)
        .then((response) => {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        })
        .finally(() => {
          hideSpinner();
        });
    },

    artistDistribution() {
      this.distributionCheck = null;
      this.distributionAmount = Number(this.stats['hung_sale_total']) +
        Number(this.stats['print_sale_total']) -
        Number(this.stats['hung_commission']) -
        Number(this.stats['print_commission']) -
        parseFloat(this.stats['distribution_total']);
      document.getElementById('pay_artist').style.display = 'block';
      showSpinner();
      apiRequest('GET', 'artshow/artist/' + this.artist.id + '/distribution',
        'max_results=all')
        .then((response) => {
          this.payments = JSON.parse(response.responseText).data;
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        })
        .finally(() => {
          hideSpinner();
        });
    },

    recordDistribution() {
      var body = [
        'amount=' + this.distributionAmount,
        'check_number=' + this.distributionCheck
      ];
      showSpinner();
      apiRequest('POST',
        'artshow/artist/' + this.artist.id + '/distribution',
        body.join('&'))
        .finally(() => {
          hideSpinner();
          this.closeDistribution();
          this.selectArtist();
        })
    },

    closeDistribution() {
      document.getElementById('pay_artist').style.display = 'none'
    },

    artistInventory() {
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
app.component('limited-text-field', limitedTextField);
app.mount('#page');

window.vue_me = app;

userProfile.options({
  prefix: 'artshow',
  title: 'Artist Information',
  updateButtonText: '',
  inlineUpdateButton: false,
  panes: ['name', 'badge', 'emailPrimary', 'phone', 'addr'],
  onChange: window.vue_me._instance.proxy.artistDataChange
});

export default app;
