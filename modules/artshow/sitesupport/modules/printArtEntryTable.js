/* jshint esversion: 6 */
/* globals apiRequest, alertbox */

import artTable from './artTable.js';

export default {
  extends: artTable,
  props: {
    requestedColumns: {
      type: Array,
      default:['name', 'type', 'quantity', 'price'],
    },
  },
  emits: [],
  created() {
    this.columns = [];
    this.values = Array.from({ length: this.printArtEntryCount }, () => ({
      name: null,
      art_type: null,
      quantity: null,
      price: null
    }));
  },
  data() {
    return {
      configuration: {
        Artshow_PieceName_Charlimit: 0
      },
      artist: null,
      printArtEntryCount: 10,
      piecetype: [],
      values: []
    }
  },
  methods: {
    configLoaded() {
      this.piecetype = [];
      for (var type in this.configuration.piecetype) {
        if (type != 'type') {
          this.piecetype.push(this.configuration.piecetype[type].piece);
        }
      }

      if (this.requestedColumns.indexOf('name') > -1) {
        this.columns.push({value: 'name', title: 'Name', length: 'Artshow_PieceName_Charlimit'});
      }
      if (this.requestedColumns.indexOf('type') > -1) {
        this.columns.push({value: 'art_type', title: 'Type', type: 'art_type'});
      }
      if (this.requestedColumns.indexOf('quantity') > -1) {
        this.columns.push({value: 'quantity', title: 'Quantity', type: 'number'});
      }
      if (this.requestedColumns.indexOf('price') > -1) {
        this.columns.push({value: 'price', title: 'Price', type: 'number'});
      }

    },

    load(artist) {
      this.artist = artist;
      this.values = Array.from({ length: this.printArtEntryCount }, () => ({
        name: null,
        art_type: this.configuration['Artshow_DefaultType'].value,
        quantity: null,
        price: null
      }));
    },

    sendPrintArt(index) {
      var name = this.values[index].name;
      if (name) {
        var type = this.values[index].art_type;
        var quantity = this.values[index].quantity;
        var price = this.values[index].price;

        var param = 'name=' + name + '&art_type=' + type + '&quantity=' +
                    quantity + '&price=' + price;

        return apiRequest('POST', 'artshow/artist/' + this.artist + '/print', param);
      }
    },

    validateArt(index) {
      var mask = ((1 << 3) - 1);
      var blank = 0;
      if (this.values[index].name == null || this.values[index].name == '') {blank |= 1 << 0;}
      if (this.values[index].quantity == null || this.values[index].quantity == 0) {blank |= 1 << 1;}
      if (this.values[index].price  == null || this.values[index].price == 0) {blank |= 1 << 2;}
      if (blank == mask) {
        return null;
      }
      return blank;
    },

    reportArtProblem(index, invalid) {
      var error = [];
      if (invalid & 1 << 0) {error.push('Name');}
      if (invalid & 1 << 1) {error.push('Quantity');}
      if (invalid & 1 << 2) {error.push('Price');}
      alertbox('Entry ' + index + ' is missing: ' + error.join(', '));
    },

    submitArt() {
      return new Promise((resolve, reject) => {
        for (var i = 0; i < this.printArtEntryCount; i++) {
          var invalid = this.validateArt(i);
          if (invalid == null) {
            continue;
          }
          if (invalid)
          {
            this.reportArtProblem(i, invalid);
            reject();
            return;
          }
        }
        var promises = Array();
        for (i = 0; i < this.printArtEntryCount; i++) {
          promises.push(this.sendPrintArt(i));
        }

        Promise.all(promises).then((response) => {
          this.debugmsg(response.responseText);
          resolve();
        })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.debugmsg(response.responseText);
            reject();
          });
      });
    },

    wholeInteger(idx, value)  {
      this.values[idx][value] = Math.floor(this.values[idx][value]);
    }
  },
  template: `
  <div class="UI-padding">
    <div class="UI-table-all">
      <div v-for="c in columns" class="UI-table-cell">
        {{c.title}}
      </div>
      <div v-for="idx in printArtEntryCount" class="UI-table-row" :key="idx">
        <div v-for="c in columns" class="UI-table-cell" :key="c.value">
          <input v-if="c.type === undefined && c.length === undefined" type=edit class="UI-input" v-model="values[idx-1][c.value]" />
          <limited-text-field v-if="c.type === undefined && c.length !== undefined" v-model="values[idx-1][c.value]"
            :char-limit=configuration[c.length].value placeholder=''> </limited-text-field>
          <input v-if="c.type == 'number'" type=number min="1" step="1" @change="wholeInteger(idx-1, c.value)" 
            class="UI-input" v-model="values[idx-1][c.value]"/>
          <select v-if="c.type == 'art_type'" class="UI-select" v-model="values[idx-1][c.value]">
            <option v-for="p in piecetype">{{p}}</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  `
};
