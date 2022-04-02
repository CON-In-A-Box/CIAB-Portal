/* jshint esversion: 6 */
/* globals apiRequest
 */

import artTable from './artTable.js';

export default {
  extends: artTable,
  props: {
    requestedColumns: {
      type: Array,
      default:['id', 'name', 'type', 'quantity', 'price', 'status', 'sold']
    },
    selectable: Boolean
  },
  emits: ['updatePrintSold', 'updateCommission'],
  created() {
    this.columns = [];
    if (this.requestedColumns.indexOf('id') > -1) {
      this.columns.push({value: 'id', title: 'Lot Number', type:'function', target: this.getLotNumber});
    }
    if (this.requestedColumns.indexOf('name') > -1) {
      this.columns.push({value: 'name', title: 'Name'});
    }
    if (this.requestedColumns.indexOf('type') > -1) {
      this.columns.push({value: 'art_type', title: 'Type'});
    }
    if (this.requestedColumns.indexOf('non_tax') > -1) {
      this.columns.push({value: 'non_tax', title: 'Not Taxable', type: 'boolean'});
    }
    if (this.requestedColumns.indexOf('charity') > -1) {
      this.columns.push({value: 'charity', title: 'Charity', type: 'boolean'});
    }
    if (this.requestedColumns.indexOf('quantity') > -1) {
      this.columns.push({value: 'quantity', title: 'Quantity'});
    }
    if (this.requestedColumns.indexOf('price') > -1) {
      this.columns.push({value: 'price', title: 'Price'});
    }
    if (this.requestedColumns.indexOf('sold') > -1) {
      this.columns.push({value: 'sold', title: 'Sold'});
    }
    if (this.requestedColumns.indexOf('location') > -1) {
      this.columns.push({value: 'location', title: 'Location'});
    }
    if (this.requestedColumns.indexOf('status') > -1) {
      this.columns.push({value: 'status', title: 'Status', type: 'function', target: this.getStatus});
    }
    if (this.requestedColumns.indexOf('notes') > -1) {
      this.columns.push({value: 'notes', title: 'Notes'});
    }
  },
  data() {
    return {
      columns: [],
      checked: [],
      prices: null,
      pieces: null,
      artist: null,
      checkAll: false,
      itemsSold: 0,
      soldValue: 0,
      commission: 0,
    };
  },
  methods: {
    loadArtist(artist) {
      this.artist = artist;
      this.commission = 0;
      return new Promise((resolve, reject) => {
        apiRequest('GET', 'artshow/artist/' + artist + '/print', null)
          .then((response) => {
            this.debugmsg(response.responseText);
            var data = JSON.parse(response.responseText);
            this.pieces = data.data;
            this.checked = Array(this.pieces.length).fill(false);
            for (var i in this.pieces) {
              var saleValue = parseInt(this.pieces[i].price) * parseInt(this.pieces[i].sold);
              this.itemsSold += parseInt(this.pieces[i].sold);
              this.soldValue += saleValue
              this.commission += saleValue * (parseInt(this.configuration.Artshow_PrintShopComission.value) / 100.0);
            }
            this.$emit('updatePrintSold', this.itemsSold, this.soldValue);
            this.$emit('updateCommission', this.commission);
            resolve();
          })
          .catch((response) => {
            if (response) {
              if (response instanceof Error) { throw response; }
              this.debugmsg(response.responseText);
            }
            this.pieces = null;
            this.itemsSold = 0;
            this.soldValue = 0;
            this.$emit('updatePrintSold', this.itemsSold, this.soldValue);
            reject();
          });
      });
    },
    printValue(piece, column) {
      if ('type' in column) {
        if (column.type == 'boolean') {
          if (parseInt(piece[column.value])) {
            return 'Yes';
          } else {
            return 'No';
          }
        }
        else if (column.type == 'function') {
          return column.target(piece);
        }
      }
      return piece[column.value];
    },
    getStatus(piece) {
      if (piece.status) {
        return piece.status;
      } else if (piece.location) {
        return 'Displayed';
      }
      return 'New';
    },
    getLotNumber(piece) {
      return parseInt(piece.id) + 1;
    },
    toggleCheckAll() {
      for (const [i, v] of this.checked.entries()) {
        if (v != !this.checkAll) {
          this.checked[i] = !this.checkAll;
        }
      }
    },
    getSelected() {
      var output = [];
      for (const [i, v] of this.pieces.entries()) {
        if (this.checked[i]) {
          output.push(v);
        }
      }
      return output;
    },
    withSelected(target) {
      var output = this.getSelected();
      if (output.length > 0) {
        var fn;
        if (target[0] == '$') {
          this.$parent[target.substring(1)](output);
        } else if (target[0] == '#') {
          fn = eval(target.substring(1));
          if (typeof fn === 'function') {
            fn.apply(null, [ output ]);
          } else {
            console.log(target + ' Callback Not Found');
          }
        } else {
          fn = window[target];
          if (typeof fn === 'function') {
            fn.apply(null, [ output ]);
          } else {
            console.log(target + ' Callback Not Found');
          }
        }
      }
    }
  },
  template: `
  <div class="UI-padding">
    <div class="UI-table-all">
      <div v-if="pieces" class="UI-table-row">
        <div v-if="selectable" class="UI-table-cell"><input type=checkbox class="UI-checkbox" @click="toggleCheckAll" v-model=checkAll></div>
        <div v-for="c in columns" class="UI-table-cell">{{c.title}}</div>
      </div>
      <div v-for="(p, index) in pieces" class="UI-table-row">
        <div v-if="selectable" class="UI-table-cell"><input type=checkbox class="UI-checkbox" v-model=checked[index] :key=index></div>
        <div v-for="c in columns" class="UI-table-cell" @click="onClick(p)">{{printValue(p, c)}}</div>
      </div>
    </div>
  </div>
  `
}
