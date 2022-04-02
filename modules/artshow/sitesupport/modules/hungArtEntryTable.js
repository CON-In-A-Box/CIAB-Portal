/* jshint esversion: 6 */
/* globals apiRequest, alertbox */

import artTable from './artTable.js';

export default {
  extends: artTable,
  props: {
    requestedColumns: {
      type: Array,
      default:['name', 'medium', 'edition', 'art_type', 'not_for_sale', 'prices']
    },
  },
  emits: [],
  created() {
    this.columns = [];
    this.art_count = 1;
    this.values = Array.from({ length: this.art_count }, () => ({
      name: null,
      medium: null,
      edition: null,
      art_type: null,
      not_for_sale: null,
      non_tax: null,
      charity: null,
    }));
  },
  data() {
    return {
      configuration: {
        Artshow_PieceName_Charlimit: 0,
        Artshow_PieceEdition_Charlimit: 0,
        Artshow_PieceMedium_Charlimit: 0
      },
      artist: null,
      art_count: 0,
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
        this.columns.push({value: 'name', title: 'Piece Name', length: 'Artshow_PieceName_Charlimit'});
      }
      if (this.requestedColumns.indexOf('medium') > -1) {
        this.columns.push({value: 'medium', title: 'Medium', length: 'Artshow_PieceMedium_Charlimit'});
      }
      if (this.requestedColumns.indexOf('edition') > -1) {
        this.columns.push({value: 'edition', title: 'Edition', length: 'Artshow_PieceEdition_Charlimit'});
      }
      if (this.requestedColumns.indexOf('art_type') > -1) {
        this.columns.push({value: 'art_type', title: 'Type', type: 'art_type'});
      }
      if (this.requestedColumns.indexOf('not_for_sale') > -1) {
        this.columns.push({value: 'not_for_sale', title: 'Not For Sale', type: 'boolean'});
      }
      if (this.requestedColumns.indexOf('non_tax') > -1) {
        this.columns.push({value: 'non_tax', title: 'Not Taxable', type: 'boolean'});
      }
      if (this.requestedColumns.indexOf('charity') > -1) {
        this.columns.push({value: 'charity', title: 'Charity', type: 'boolean'});
      }

      if (this.requestedColumns.indexOf('prices') > -1) {
        for (var price in this.configuration.pricetype) {
          if (this.configuration.pricetype[price].artist_set == '1') {
            this.columns.push({title: this.configuration.pricetype[price].price, value: this.configuration.pricetype[price].price, type: 'price'});
          }
        }
      }
    },

    load(artist, max) {
      this.art_count = max;
      this.artist = artist;

      this.values = Array.from({ length: this.art_count }, () => ({
        name: null,
        medium: null,
        edition: null,
        art_type: null,
        not_for_sale: null,
        non_tax: null,
        charity: null,
      }));

      for (var i in this.values)  {
        this.values[i].art_type = this.configuration['Artshow_DefaultType'].value;
      }

      if (this.requestedColumns.indexOf('prices') > -1) {
        for (var price in this.configuration.pricetype) {
          if (this.configuration.pricetype[price].artist_set == '1') {
            for (i in this.values) {
              this.values[i][this.configuration.pricetype[price].price] = null;
            }
          }
        }
      }
    },

    sendHungArt(index) {
      var name = this.values[index].name;
      if (name) {
        var medium = this.values[index].medium;
        var edition = this.values[index].edition;
        var type = this.values[index].art_type;
        var nfs = 0;

        if (this.values[index].not_for_sale) {
          nfs = 1;
        }

        var param = 'name=' + name + '&medium=' + medium + '&edition=' +
                    edition + '&art_type=' + type + '&not_for_sale=' + nfs;

        for (var data in this.configuration.pricetype) {
          if (this.configuration.pricetype[data].artist_set == '1') {
            name = this.configuration.pricetype[data].price.replace(' ','%20');
            var value = this.values[index][this.configuration.pricetype[data].price]
            if (value !== null && value !== '') {
              param += '&' + name + '=' + value;
            }
          }
        }

        return apiRequest('POST', 'artshow/artist/' + this.artist + '/art',
          param);
      }
    },

    validateArt(index) {
      var blank = 0;
      if (this.values[index].name == null || this.values[index].name == '') {blank |= 1 << 0;}
      if (this.values[index].medium == null || this.values[index].medium == '') {blank |= 1 << 1;}
      if (this.values[index].edition == null || this.values[index].edition == '') {blank |= 1 << 2;}

      var offset = 3;
      if (this.values[index].not_for_sale != '1') {
        for (var data in this.configuration.pricetype) {
          if (this.configuration.pricetype[data].artist_set == '1') {
            if (this.values[index][this.configuration.pricetype[data].price] == null ||
              this.values[index][this.configuration.pricetype[data].price] == '' ||
              this.values[index][this.configuration.pricetype[data].price] < 1) {
              blank |= 1 << offset;
            }
            offset++;
          }
        }
      }
      if (blank == ((1 << offset) - 1)) {
        return null;
      }

      return blank;
    },

    reportArtProblem(index, invalid) {
      var error = [];
      var nfs = (this.values[index].not_for_sale == '1');
      if (invalid & 1 << 0) {error.push('Name');}
      if (invalid & 1 << 1) {error.push('Medium');}
      if (invalid & 1 << 2) {error.push('Edition');}
      if (!nfs && invalid >= 1 << 3) {error.push('Valid Prices');}
      alertbox('Entry ' + (index + 1) + ' is missing: ' + error.join(', '));
    },

    submitArt() {
      return new Promise((resolve, reject) => {
        for (var i = 0; i < this.art_count; i++) {
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
        for (i = 0; i < this.art_count; i++) {
          promises.push(this.sendHungArt(i));
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
      <div v-for="idx in art_count" class="UI-table-row" :key="idx">
        <div v-for="c in columns" class="UI-table-cell" :key="c.value">
          <input v-if="c.type === undefined && c.length === undefined" type=edit class="UI-input" v-model="values[idx-1][c.value]" />
          <limited-text-field v-if="c.type === undefined && c.length !== undefined" v-model="values[idx-1][c.value]"
            :char-limit=configuration[c.length].value placeholder=''> </limited-text-field>
          <input v-if="c.type == 'number'" type=number min="1" step="1" class="UI-input"
            v-model="values[idx-1][c.value]" @change="wholeInteger(idx-1, c.value)"/>
          <input v-if="c.type == 'price'" type=number min="1" step="1" class="UI-input"
            v-model="values[idx-1][c.value]" @change="wholeInteger(idx-1, c.value)"
            :disabled="values[idx-1].not_for_sale == '1'"/>
          <select v-if="c.type == 'art_type'" class="UI-select" v-model="values[idx-1][c.value]">
            <option v-for="p in piecetype">{{p}}</option>
          </select>
          <input v-if="c.type == 'boolean'" type=checkbox class="UI-checkbox UI-margin"
            v-model="values[idx-1][c.value]"  true-value="1" false-value="0" />
        </div>
      </div>
    </div>
  </div>
  `
};
