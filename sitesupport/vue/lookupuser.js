/*
 * Vue component for users lookup element
 */
/* jshint esversion: 6 */
/* globals escapeHtml, apiRequest, basicBackendRequest, QuaggaApp */

export default {
  props: {
    urlTag : {
      type: String,
      default: 'memberId'
    },
    prompt: {
      type: String,
      default: 'Member Badge Number, E-Mail or Full Name'
    },
    badgeName: {
      type: Boolean,
      default: true
    },
    partialMatch: {
      type: Boolean,
      default: false
    },
    lookupParam: {
      type:String,
      default: 'lookupId'
    },
    lookupTarget: [String, Function],
    initialId: [String, Function],

    onHandler: [String, Function],
    onSuccess: [String, Function],
    onFail: [String, Function],
  },
  emits: [
    'prelookup'
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
      showSpinner: false,
      target: null,
      possibleMembers: null
    }
  },
  methods: {
    lookupSuccess(response) {
      if (response.length == 1) {
        this.message = escapeHtml('Found ' +
            response[0]['First Name'] + ' ' +
            response[0]['Last Name']);
      } else if (response.length > 1) {
        this.possibleMembers = response;
        return;
      }
      if (this.target) {
        this.gotoTarget(response[0]);
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
      else if (code == 409) {
        this.message = user + ' has too many matches.';
      }
    },
    gotoTarget(item) {
      this.id = item.id;
      this.possibleMembers = null;
      this.$emit('handler', this, item);
      if (this.$props && this.$props.onHandler) { return; }
      var newTarget = '';
      var i = this.target.indexOf(this.urlTag + '=');
      var uid = null;
      if (item) {
        uid = item.Id;
      }
      if (uid) {
        if (i != -1) {
          var regexp = new RegExp('(' + this.urlTag + '=).*?($)');
          newTarget = this.target.replace(regexp, '$1' + uid);
        } else {
          newTarget = this.target + '&' + this.urlTag + '=' + uid;
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
      this.showSpinner = false;
    },
    clearFailure() {
      this.messageClass = null;
      this.showSpinner = false;
    },
    clear() {
      this.messageClass = null;
      this.showSpinner = false;
      this.id = null;
      this.message = null;
    },
    lookupId() {
      this.$emit('prelookup');
      if (this.id) {
        this.target = window.location.href;
        this.showSpinner = true;
        this.message = null;
        this.messageClass = null;
        if (this.lookupTarget !== undefined) {
          var parameters = this.lookupParam + '=' + this.id;
          if (this.badgeName) {
            parameters += '&useBadgeName=1';
          }
          if (this.partialMatch) {
            parameters += '&partialMatch=1';
          }
          basicBackendRequest('GET', this.lookupTarget, parameters,
            (input) => {
              var response = JSON.parse(input.responseText);
              this.clearFailure();
              this.$emit('success', this, this.target, response);
              if (!this.$props || !this.$props.onSuccess) {
                this.lookupSuccess(response);
              }
            },
            (input) => {
              this.$emit('fail', this, this.target, input.responseText, this.id, input.status);
              if (!this.$props || !this.$props.onFail) {
                this.lookupFailed(input.responseText, this.id, input.status);
              }
            });
        } else {
          var query = 'q=' + this.id;
          if (this.badgeName) {
            query += '&from=all';
          } else {
            query += '&from=email,id,legal_name,name,badge_id';
          }
          if (this.partialMatch) {
            query += '&partial=true'
          }
          apiRequest('GET', 'member/find', query)
            .then((input) => {
              var response = JSON.parse(input.responseText);
              this.clearFailure();
              var data = Object.values(response.data);
              /* existing code's paticular parameters */
              data.forEach((item) => {
                item['First Name'] = item.first_name;
                item['Last Name'] = item.last_name;
                item['Email'] = item.email;
                item['Id'] = item.id;
              })
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
            })
        }
      } else {
        if (this.target) {
          this.gotoTarget(null);
        }
      }
    },
    handleKeydown() {
      this.possibleMembers = null;
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
    }
  },
  template:`
  <div class="UI-bar">
    <label class="UI-padding UI-bar-item">{{prompt}}</label>
    <div class="UI-bar">
      <input class="UI-input UI-bar-item UI-padding" @change="lookupId"
        @keydown="handleKeydown" placeholder="(badge #, email, Name)"
        required="" autocomplete="off"  v-model="id">
      <button type="button" class="icon-barcode button-scan UI-lookup-user-button"
        @click="handleBarcodeClick">
      </button>
      <span class="UI-bar-item" v-if="showSpinner">
        <i class='fas fa-spinner UI-spin'></i>
      </span>
      <span class="UI-bar-item" :class="messageClass" >{{message}}</span>
      <div class="UI-lookup-user-dropdown">
        <div v-for="person in possibleMembers" class="UI-bar-item UI-button" @click="gotoTarget(person)">
          {{person.Id}} : {{person['First Name']}} {{person['Last Name']}} ({{person.Email}})
        </div>
      </div>
    </div>
  </div>
  `
}
