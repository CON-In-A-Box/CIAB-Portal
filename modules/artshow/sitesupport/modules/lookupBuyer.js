/*
 * Vue component for buyer lookup element
 */
/* jshint esversion: 6 */
/* globals escapeHtml, apiRequest, QuaggaApp, showSpinner, hideSpinner */

export default {
  props: {
    prompt: {
      type: String,
      default: 'Buyer Badge Number or Full Name'
    },
    addNew: {
      type: Boolean,
      default: false
    },
    onHandler: [String, Function]
  },
  emits: [
    'handler',
  ],
  created() {
  },
  mounted() {
  },
  data() {
    return {
      message: null,
      id: null,
      messageClass: null,
      possibleMembers: null,
      lookupTarget: null,
      notFound: false
    }
  },
  methods: {
    lookupSuccess(response) {
      this.message = escapeHtml('Found ' +
          response.identifier);
      this.$emit('handler', this, response);
    },
    lookupFailed(response, user, code) {
      this.markFailure();
      if (code == 400) {
        this.message = user + ' invalid lookup.';
      }
      else if (code == 404) {
        this.message = user + ' not found.';
        if (this.addNew) {
          this.notFound = true;
        }
      }
      else if (code == 409) {
        this.message = user + ' has too many matches.';
      }
    },
    markFailure() {
      this.messageClass = 'UI-red';
    },
    clearFailure() {
      this.messageClass = null;
    },
    clear() {
      this.messageClass = null;
      hideSpinner();
      this.id = null;
      this.message = null;
    },
    handleChanged() {
      if (this.possibleMembers !== null) {
        this.lookupId();
      }
    },
    lookupId() {
      this.notFound = false;
      if (this.id) {
        showSpinner();
        this.message = null;
        this.messageClass = null;
        apiRequest('GET', 'artshow/customer/find/' + this.id, null)
          .then((response) => {
            var data = JSON.parse(response.responseText);
            this.clearFailure();
            this.lookupSuccess(data);
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.lookupFailed(response.responseText, this.id, response.status);
          })
          .finally(() => {
            hideSpinner();
          });
      }
    },
    handleKeydown(event) {
      this.possibleMembers = null;
      if (event.keyCode == 13) {
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
    addBuyer() {
      showSpinner();
      apiRequest('POST', 'artshow/customer', 'identifier=' + this.id)
        .then(() => { this.lookupId(); })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        })
        .finally(() => {
          hideSpinner();
        });
    },
  },
  template:`
  <div class="UI-bar">
    <label class="UI-padding UI-bar-item">{{prompt}}</label>
    <div class="UI-bar">
      <input class="UI-input UI-bar-item UI-padding" @change="handleChanged"
        @keydown="handleKeydown" placeholder="(badge #, email, Name)"
        required="" autocomplete="off"  v-model="id">
      <button type="button" class="icon-barcode button-scan UI-lookup-user-button"
        @click="handleBarcodeClick">
      </button>
      <span class="UI-bar-item" :class="messageClass" >{{message}}</span>
      <button v-if="notFound" type="button" class="UI-eventbutton"
        @click="addBuyer">
        Add new buyer
      </button>
    </div>
  </div>
  `
}
