/* jshint esversion: 6 */
/* globals apiRequest
 */

import artTable from './artTable.js';

export default {
  extends: artTable,
  props: {
    requestedColumns: {
      type: Array,
      default:['name', 'artist', 'price_type', 'sale_price', 'status']
    },
    selectable: Boolean
  },
  created() {
    this.columns = [];
    if (this.requestedColumns.indexOf('id') > -1) {
      this.columns.push({value: 'piece.id', title: 'Piece Number'});
    }
    if (this.requestedColumns.indexOf('name') > -1) {
      this.columns.push({value: 'piece.name', title: 'Piece Name'});
    }
    if (this.requestedColumns.indexOf('artist') > -1) {
      this.columns.push({value: 'piece.artist', title: 'Artist', type:'artist'});
    }
    if (this.requestedColumns.indexOf('medium') > -1) {
      this.columns.push({value: 'piece.medium', title: 'Medium'});
    }
    if (this.requestedColumns.indexOf('edition') > -1) {
      this.columns.push({value: 'piece.edition', title: 'Edition'});
    }
    if (this.requestedColumns.indexOf('art_type') > -1) {
      this.columns.push({value: 'piece.art_type', title: 'Type'});
    }
    if (this.requestedColumns.indexOf('not_for_sale') > -1) {
      this.columns.push({value: 'piece.not_for_sale', title: 'Not For Sale', type: 'boolean'});
    }
    if (this.requestedColumns.indexOf('non_tax') > -1) {
      this.columns.push({value: 'piece.non_tax', title: 'Not Taxable', type: 'boolean'});
    }
    if (this.requestedColumns.indexOf('charity') > -1) {
      this.columns.push({value: 'piece.charity', title: 'Charity', type: 'boolean'});
    }
  },
  data() {
    return {
      columns: [],
      configuration: null,
      prices: null,
      pieces: null,
      buyer: null,
      cost: -1
    };
  },
  methods: {
    configLoaded() {
      if (this.requestedColumns.indexOf('prices') > -1) {
        for (var price in this.configuration.pricetype) {
          if (this.configuration.pricetype[price].artist_set == '1') {
            this.columns.push({title: this.configuration.pricetype[price].price,
              value: 'piece.'.this.configuration.pricetype[price].price, type: 'price'});
          }
        }
      }
      if (this.requestedColumns.indexOf('location') > -1) {
        this.columns.push({value: 'piece.location', title: 'Location'});
      }
      if (this.requestedColumns.indexOf('notes') > -1) {
        this.columns.push({value: 'piece.notes', title: 'Notes'});
      }
      if (this.requestedColumns.indexOf('price_type') > -1) {
        this.columns.push({value: 'price_type', title: 'Sale Type'});
      }
      if (this.requestedColumns.indexOf('sale_price') > -1) {
        this.columns.push({value: 'price', title: 'Price', type: 'price'});
      }
    },
    loadList(buyer) {
      this.prices = null;
      this.pieces = null;
      this.cost = -1;
      this.buyer = buyer;
      return new Promise((resolve, reject) => {
        apiRequest('GET', 'artshow/customer/' + buyer + '/sales/art', 'max_results=all')
          .then((response) => {
            this.debugmsg(response.responseText);
            var data = JSON.parse(response.responseText);
            this.pieces = data.data;
            this.cost = -1;
            apiRequest('GET', 'artshow/customer/' + buyer + '/sales/print', 'max_results=all')
              .then((response) => {
                this.debugmsg(response.responseText);
                var data = JSON.parse(response.responseText);
                for (const index in data.data) {
                  data.data[index]['price_type'] = 'Direct';
                }
                if (this.pieces) {
                  this.pieces = this.pieces.concat(data.data);
                } else {
                  this.pieces = data.data;
                }
                this.cost = -1;
              })
              .finally(() => {
                resolve();
              });
          })
          .catch((response) => {
            if (response instanceof Error) { throw response; }
            this.debugmsg(response.responseText);
            apiRequest('GET', 'artshow/customer/' + buyer + '/sales/print', 'max_results=all')
              .then((response) => {
                this.debugmsg(response.responseText);
                var data = JSON.parse(response.responseText);
                for (const index in data.data) {
                  data.data[index]['price_type'] = 'Direct';
                }
                if (this.pieces) {
                  this.pieces = this.pieces.concat(data.data);
                } else {
                  this.pieces = data.data;
                }
                resolve();
              })
              .catch((response) => {
                if (response instanceof Error) { throw response; }
                this.debugmsg(response.responseText);
                reject();
              });
          });
      });
    },
    getCost() {
      if (this.cost < 0) {
        this.cost = 0;
        for (var p in this.pieces) {
          this.cost += parseFloat(this.pieces[p].price);
        }
      }
      return this.cost;
    },
    printValue(sale, column) {
      var data = sale;
      var name = column.value;
      if (column.value.includes('piece.')) {
        data = sale.piece;
        name = column.value.split('.')[1];
      }
      if ('type' in column) {
        if (column.type == 'boolean') {
          if (parseInt(data[name])) {
            return 'Yes';
          } else {
            return 'No';
          }
        }
        else if (column.type == 'price') {
          return '$' + data[name];
        }
        else if (column.type == 'function') {
          return column.target(data);
        }
        else if (column.type == 'artist') {
          if (data.artist.company_name_on_sheet != '0') {
            return data.artist.company_name;
          } else {
            return data.artist.member.first_name + ' ' + data.artist.member.last_name;
          }
        }
      }
      return data[name];
    }
  },
  template: `
  <div class="UI-padding">
    <div class="UI-table-all">
      <div v-if="pieces" class="UI-table-row">
        <div v-for="c in columns" class="UI-table-cell">{{c.title}}</div>
      </div>
      <div v-for="(p, index) in pieces" class="UI-table-row">
        <div v-for="c in columns" class="UI-table-cell">{{printValue(p, c)}}</div>
      </div>
    </div>
  </div>
  `
}
