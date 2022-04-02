/* jshint esversion: 6 */
/* globals apiRequest */

export default {
  props: {
    debug: Boolean,
    debugElement: {
      type: String,
      default: 'headline_section'
    },
    readOnly: {
      type: Boolean,
      default: false
    },
    requestedFields: {
      type: Array,
      default: ['name', 'artist', 'art_type', 'price', 'quantity', 'sold']
    }
  },
  mounted() {
  },
  created() {
    this.fields = [];
    if (this.requestedFields.indexOf('id') > -1) {
      this.fields.push({value: 'id', title: 'Lot Number', type:'read-only'});
    }
    if (this.requestedFields.indexOf('name') > -1) {
      this.fields.push({value: 'name', title: 'Name', length: 'Artshow_PieceName_Charlimit'});
    }
    if (this.requestedFields.indexOf('artist') > -1) {
      this.fields.push({value: 'artist', title: 'Artist', type:'artist'});
    }
    if (this.requestedFields.indexOf('art_type') > -1) {
      this.fields.push({value: 'art_type', title: 'Type', type:'art_type'});
    }
    if (this.requestedFields.indexOf('non_tax') > -1) {
      this.fields.push({value: 'non_tax', title: 'Not Taxable', type: 'boolean'});
      this.booleans['non_tax'] = false;
    }
    if (this.requestedFields.indexOf('charity') > -1) {
      this.fields.push({value: 'charity', title: 'Charity', type: 'boolean'});
      this.booleans['charity'] = false;
    }
    if (this.requestedFields.indexOf('price') > -1) {
      this.fields.push({value: 'price', title: 'Price', type: 'number'});
    }
    if (this.requestedFields.indexOf('quantity') > -1) {
      this.fields.push({value: 'quantity', title: 'Quantity', type: 'number'});
    }
    if (this.requestedFields.indexOf('sold') > -1) {
      this.fields.push({value: 'sold', title: 'Sold', type:'read-only'});
    }
    apiRequest('GET', 'artshow/', 'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        this.configLoaded();
      });
    this.isReadOnly = this.readOnly;
  },
  data() {
    return {
      configuration: {
        Artshow_PieceName_Charlimit: 0
      },
      piecetype: [],
      fields: [],
      piece: {},
      changes: {},
      artist: null,
      isReadOnly: false,
      booleans: {}
    };
  },
  methods: {
    debugmsg(message) {
      if (this.debug && this.debugElement) {
        var target = document.getElementById(this.debugElement);
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },
    configLoaded() {
      this.piecetype = [];
      for (var type in this.configuration.piecetype) {
        if (type != 'type') {
          this.piecetype.push(this.configuration.piecetype[type].piece);
        }
      }
      if (this.requestedFields.indexOf('location') > -1) {
        this.fields.push({value: 'location', title: 'Location'});
      }
      if (this.requestedFields.indexOf('notes') > -1) {
        this.fields.push({value: 'notes', title: 'Notes'});
      }
    },
    loadPiece(id, eventId) {
      var uri = 'artshow/print/' + id;
      if (eventId) {
        uri += '/' + eventId;
      }
      return new Promise((resolve, reject) => {
        apiRequest('GET', uri, '')
          .then((response) => {
            this.debugmsg(response.responseText);
            var data = JSON.parse(response.responseText);
            this.displayPiece(data.data);
            resolve();
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.debugmsg(response.responseText);
            reject();
          });
      });
    },
    savePiece() {
      this.changes['id'] = this.piece.id;
      return new Promise((resolve, reject) => {
        var param = '';
        for (var key in this.changes) {
          if (key != 'id' && this.piece[key] != this.changes[key]) {
            if (param != '') {
              param += '&';
            }
            param += key + '=' + encodeURIComponent(this.changes[key]);
          }
        }
        apiRequest('PUT', 'artshow/print/' + this.piece.id, param)
          .then((response) => {
            this.debugmsg(response.responseText);
            resolve(response);
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.debugmsg(response.responseText);
            reject(response);
          })
      })
    },
    displayArt(data) {
      this.changes = {};
      this.piece = data;
      for (var entry in data) {
        if (entry in this.booleans) {
          this.booleans[entry] = data[entry];
        }
        this.changes[entry] = data[entry];
      }
      if (data.artist.company_name_on_sheet != '0') {
        this.artist = data.artist.company_name;
      } else {
        this.artist = data.artist.member.first_name +
                      ' ' + data.artist.member.last_name;
      }
      this.scalePiece();
    },
    scalePiece() {
      var element = this.$el.parentElement;
      var height = element.offsetHeight;
      var fit = window.innerHeight - 100;
      if (height > fit) {
        var factor = fit / height;
        element.style.transform = 'translate(0%, -' +
          (100 - (factor * 100)) / 2 + '%)';
        element.style.transform += 'scale(' + factor + ')';
      } else {
        element.style.transform = 'scale(1)';
        element.style.transform += 'translate(0px, 0px)';
      }
    },
    markReadOnly() {
      this.isReadOnly = true;
    },
    onChange(item) {
      if (item.type == 'boolean') {
        this.changes[item.value] = this.booleans[item.value];
      }
    }
  },
  template: `
  <div class="UI-padding UI-container">
    <div v-for="f in fields">
     <div class="UI-container">
     <label class="UI-label">{{f.title}}</label>
     <input v-if="f.type === undefined && f.length === undefined" type=edit
      class="UI-input" v-model="changes[f.value]" :disabled="isReadOnly" />
     <div v-if="f.type === undefined && f.length !== undefined">
       <input type=edit
        class="UI-input" v-model="changes[f.value]"
        :disabled="isReadOnly" :maxlength=configuration[f.length].value
        :style="{'float': 'left', 'width':configuration[f.length].value+'ch',
                 'max-width': '75%'}"
        />
        <span v-if="piece[f.value]" class="UI-margin" style="float: left; color:lightgrey; font-size:50%;">
          {{ changes[f.value].length }}/{{ configuration[f.length].value }}
        </span>
     </div>
     <input v-if="f.type == 'number'" type=number min="1" class="UI-input"
      :value=piece[f.value] @change="onChange(f)" :disabled="isReadOnly"/>
     <input v-if="f.type == 'read-only'" type=edit class="UI-input" disabled :value=piece[f.value] />
     <input v-if="f.type == 'artist'" type=edit class="UI-input" disabled :value=artist />
     <input v-if="f.type == 'boolean'" type=checkbox class="UI-checkbox UI-margin"
      v-model=booleans[f.value]  @change="onChange(f)" :disabled="isReadOnly"
      true-value="1" false-value="0"
     />
     <select v-if="f.type == 'art_type'" class="UI-select" :value=piece[f.value] @change="onChange(f)" :disabled="isReadOnly">
     <option v-for="p in piecetype">{{p}}</option>
     </select>
     </div>
   </div>
  </div>
  `
}
