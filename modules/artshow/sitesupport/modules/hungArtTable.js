/* jshint esversion: 6 */
/* globals apiRequest
 */

import artTable from './artTable.js';

export default {
  extends: artTable,
  props: {
    requestedColumns: {
      type: Array,
      default:['id', 'name', 'medium', 'edition', 'art_type',
        'not_for_sale', 'prices', 'fee', 'status']
    },
    selectable: Boolean
  },
  emits: ['updateArtSold', 'updateFee', 'updateCommission'],
  created() {
    this.columns = [];
    if (this.requestedColumns.indexOf('id') > -1) {
      this.columns.push({value: 'id', title: 'Piece Number'});
    }
    if (this.requestedColumns.indexOf('name') > -1) {
      this.columns.push({value: 'name', title: 'Piece Name'});
    }
    if (this.requestedColumns.indexOf('medium') > -1) {
      this.columns.push({value: 'medium', title: 'Medium'});
    }
    if (this.requestedColumns.indexOf('edition') > -1) {
      this.columns.push({value: 'edition', title: 'Edition'});
    }
    if (this.requestedColumns.indexOf('art_type') > -1) {
      this.columns.push({value: 'art_type', title: 'Type'});
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
  },
  data() {
    return {
      columns: [],
      checked: [],
      configuration: null,
      prices: null,
      pieces: null,
      artist: null,
      fees: 0,
      commission: 0,
      itemsSold: 0,
      soldValue: 0,
      checkAll: false
    };
  },
  methods: {
    configLoaded() {
      if (this.requestedColumns.indexOf('prices') > -1) {
        for (var price in this.configuration.pricetype) {
          if (this.configuration.pricetype[price].artist_set == '1') {
            this.columns.push({title: this.configuration.pricetype[price].price, value: this.configuration.pricetype[price].price, type: 'price'});
          }
        }
      }
      if (this.requestedColumns.indexOf('fee') > -1) {
        this.columns.push({value:'fee', title: 'Hanging Fee', type: 'function', target: this.getFee});
      }
      if (this.requestedColumns.indexOf('location') > -1) {
        this.columns.push({value: 'location', title: 'Location'});
      }
      if (this.configuration.Artshow_ArtAuction && this.requestedColumns.indexOf('auction') > -1) {
        this.columns.push({value: 'in_auction', title: 'In Auction', type: 'boolean'});
      }
      if (this.requestedColumns.indexOf('status') > -1) {
        this.columns.push({value: 'status', title: 'Status', type: 'function', target: this.getStatus});
      }
      if (this.requestedColumns.indexOf('notes') > -1) {
        this.columns.push({value: 'notes', title: 'Notes'});
      }
    },
    loadArtist(artist) {
      this.artist = artist;
      this.fees = 0;
      this.commission = 0;
      return new Promise((resolve, reject) => {
        apiRequest('GET', 'artshow/artist/' + artist + '/art', null)
          .then((response) => {
            this.debugmsg(response.responseText);
            var data = JSON.parse(response.responseText);
            this.pieces = data.data;
            this.checked = Array(this.pieces.length).fill(false);
            for (var p in this.pieces) {
              apiRequest('GET', 'artshow/sale/art/find/' + this.pieces[p].id, null)
                .then((response) => {
                  var sale = JSON.parse(response.responseText);
                  for (var q in this.pieces) {
                    if (this.pieces[q].id == sale.piece) {
                      this.pieces[q].status = 'Sold: ' + sale.price_type + ' ($' + sale.price + ')';
                      this.itemsSold += 1;
                      this.soldValue += parseInt(sale.price);
                      this.commission += parseInt(sale.price) * (parseInt(this.configuration.Artshow_DisplayComission.value) / 100.0);
                      this.$emit('updateArtSold', this.itemsSold, this.soldValue);
                      this.$emit('updateCommission', this.commission);
                    }
                  }
                })
                .catch((response) => {
                  if (response instanceof Error) { throw response; }
                });

              this.fees += this.getFee(this.pieces[p]);
            }
            this.$emit('updateFee', this.fees);
            resolve();
          })
          .catch((response) => {
            if (response) {
              if (response instanceof Error) { throw response; }
              this.debugmsg(response.responseText);
              this.itemsSold = 0;
              this.soldValue = 0;
              this.$emit('updateArtSold', this.itemsSold, this.soldValue);
            }
            this.pieces = null;
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
        else if (column.type == 'price') {
          if (piece.not_for_sale == '1') {
            return 'nfs';
          }
        }
      }
      return piece[column.value];
    },
    getFee(piece) {
      if (piece.not_for_sale == '1') {
        return parseInt(this.configuration.Artshow_NFSHangingFee.value);
      } else {
        return parseInt(this.configuration.Artshow_HangingFee.value);
      }
    },
    getStatus(piece) {
      if (piece.status) {
        return piece.status;
      } else {
        if (piece.in_auction == '1') {
          return 'In Auction';
        }
        if (piece.location) {
          return 'Displayed';
        }
        else if (parseInt(piece.tag_print_count)) {
          return 'Printed';
        } else {
          return 'New';
        }
      }
    },
    clear() {
      this.fees = 0;
      this.commission = 0;
      this.$emit('updateFee', 0);
      this.$emit('updateCommission', 0);
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
