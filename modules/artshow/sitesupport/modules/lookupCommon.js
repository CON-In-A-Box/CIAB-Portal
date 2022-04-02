/*
 * Base function to lookup artshow piece
 */
/* jshint esversion: 6 */
/* globals escapeHtml, apiRequest, QuaggaApp, hideSpinner, showSpinner */

export default {
  props: {
    urlTag : {
      type: String,
      default: 'memberId'
    },
    prompt: {
      type: String,
      default: 'Art Show Piece ID'
    },
    initialId: String,

    onFail: [String, Function],
    onSuccess: [String, Function],
    onHandler: [String, Function]
  },
  emits: [
    'fail',
    'success',
    'prelookup',
    'handler',
  ],
  created() {
  },
  mounted() {
    if (this.initialId !== undefined) {
      this.id = this.initialId;
    }
  },
  data() {
    return {
      message: null,
      id: null,
      messageClass: null,
      target: null,
      possiblePieces: null,
      lookupUri: null,
      searchReference: null
    }
  },
  methods: {
    lookupSuccess(response) {
      var piece = response;
      this.message = escapeHtml('Found ' + piece.name + ' by ' + this.generateArtistName(piece.artist));
      if (this.target) {
        this.gotoTarget(piece);
      }
    },
    lookupFailed(response, user, code) {
      this.markFailure();
      if (code == 400) {
        this.message = user + ' invalid lookup.';
      }
      else if (code == 404) {
        this.message = user + ' not found.';
      }
    },
    gotoTarget(item) {
      this.$emit('handler', this, item);
      if (this.$props && this.$props.onHandler) { return; }
      var newTarget = '';
      var i = this.target.indexOf(this.urlTag + '=');
      var id = null;
      if (item) {
        id = item.Id;
      }
      if (id) {
        if (i != -1) {
          var regexp = new RegExp('(' + this.urlTag + '=).*?($)');
          newTarget = this.target.replace(regexp, '$1' + id);
        } else {
          newTarget = this.target + '&' + this.urlTag + '=' + id;
        }
      } else {
        if (i != -1) {
          i--;
          newTarget = this.target.substring(0, i);
        }
      }
      window.location = newTarget;
    },
    markFailure() {
      this.messageClass = 'UI-red';
      hideSpinner();
    },
    clearFailure() {
      this.messageClass = null;
      hideSpinner();
    },
    clear() {
      this.messageClass = null;
      hideSpinner();
      this.id = null;
      this.message = null;
    },
    handleChanged() {
      this.target = window.location.href;
      if (this.searchReference == null) {
        this.lookupId();
      }
    },
    lookupId() {
      this.$emit('prelookup');
      if (this.id) {
        var res = this.id.split(':');
        showSpinner();
        this.message = null;
        this.messageClass = null;
        var url = this.lookupUri + res[0];
        if (res.length > 1) {
          url += '/' + res[1];
        }
        apiRequest('GET', url, null)
          .then((response) => {
            this.clearFailure();
            var data = JSON.parse(response.responseText);
            this.$emit('success', this, this.target, data);
            if (!this.$props || !this.$props.onSuccess) {
              this.lookupSuccess(data);
            }
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.$emit('fail', this, this.target, response.responseText, this.id, response.status);
            if (!this.$props || !this.$props.onFail) {
              this.lookupFailed(response.responseText, this.id, response.status);
            }
          });
      } else {
        if (this.target) {
          this.gotoTarget(null);
        }
      }
    },
    handleKeydown(event) {
      if (event.keyCode == 13) {
        this.target = window.location.href;
        this.lookupId();
        return false;
      }
      return true;
    },
    handleBarcodeClick() {
      QuaggaApp.init(null, this.fromQuagga);
    },
    set(value) {
      this.id = value;
    },
    fromQuagga(value) {
      this.id = value;
      this.lookupId();
    },
    generateArtistName(artist) {
      if (artist.company_name_on_sheet == '1') {
        return artist.company_name;
      }
      return artist.member.first_name + ' ' + artist.member.last_name;
    },
    foundResult(item) {
      this.id = item.id;
      this.lookupSuccess(item);
      this.$refs[this.searchReference].close();
    }
  },
  template:`
  <div class="UI-bar">
    <label class="UI-padding UI-bar-item">{{prompt}}</label>
    <div class="UI-bar">
      <input class="UI-input UI-bar-item UI-padding" @change="handleChanged"
        @keydown="handleKeydown" placeholder="(Id)"
        required="" autocomplete="off"  v-model="id">
      <button type="button" class="icon-barcode button-scan UI-lookup-user-button"
        @click="handleBarcodeClick">
      </button>
      <span class="UI-bar-item" :class="messageClass" >{{message}}</span>
    </div>
  </div>
  `
}
