/* jshint esversion: 6 */
/* globals Vue, apiRequest, confirmbox, alertbox */

import lookupUser from '../../../../sitesupport/vue/lookupuser.js'
import lookupPiece from '../../sitesupport/modules/lookupPiece.js'
import lookupPrint from '../../sitesupport/modules/lookupPrint.js'
import lookupBuyer from '../../sitesupport/modules/lookupBuyer.js'
import findArt from '../../sitesupport/modules/findArt.js'
import findPrint from '../../sitesupport/modules/findPrint.js'
import pieceInfo from '../../sitesupport/modules/piece.js'
import printInfo from '../../sitesupport/modules/print.js'

var app = Vue.createApp({
  created() {
    apiRequest('GET', 'artshow/', 'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        this.configLoaded();
      });
  },
  data() {
    return {
      debug: false,
      debugElement: 'headline_section',
      configuration: {
        Artshow_DisplayArtName: {},
        Artshow_PrintArtName: {},
      },
      customer: null,
      piece: null,
      prices: null,
      salesTypeIndex: null,
      salePrice: null,
      saleReady: false,
      printSold: false,
      artSold: false,
      buyer: null,
      print: null,
      printPrice: null,
      printQuantity: 1,
      printSaleReady: false,
    }
  },
  methods: {
    debugmsg(message) {
      if (this.debug && this.debugElement) {
        var target = document.getElementById(this.debugElement);
        if (target) {
          target.classList.add('UI-show');
          target.innerHTML = message;
        }
      }
    },
    configLoaded() {
      this.prices = [];
      for (var price in this.configuration.pricetype) {
        if (price != 'type') {
          this.prices.push(this.configuration.pricetype[price]);
        }
      }
      this.salesTypeIndex = 0;
    },
    lookupUser(lookup, item) {
      this.customer = item;
      this.debugmsg(JSON.stringify(this.customer));
      this.checkEnableSale();
      this.checkEnablePrintSale();
    },
    lookupPiece(origin, item) {
      if (item == null) {
        this.piece = null;
        this.artSold = false;
        this.buyer = null;
        this.salesTypeIndex = null;
        return;
      }
      apiRequest('GET', 'artshow/sale/art/find/' + item.id, '')
        .then((response) => {
          this.debugmsg(response);
          var data = JSON.parse(response.responseText);
          this.piece = item;
          this.debugmsg(JSON.stringify(this.piece));
          this.$refs.pce.displayPiece(this.piece);
          this.quickSalePrice = item['Quick Sale'];
          this.salePrice = this.piece[this.prices[this.salesTypeIndex].price];
          this.alreadySold(data);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }

          this.piece = item;
          this.debugmsg(JSON.stringify(this.piece));
          this.$refs.pce.displayPiece(this.piece);
          this.quickSalePrice = item['Quick Sale'];
          this.salePrice = this.piece[this.prices[this.salesTypeIndex].price];
          this.checkEnableSale();
          this.artSold = false;
          this.buyer = null;
        });
    },
    typeChanged() {
      this.salePrice = this.piece[this.prices[this.salesTypeIndex].price];
      this.checkEnableSale();
    },
    checkEnableSale() {
      this.saleReady = (!this.artSold && this.piece != null && this.customer != null && this.salesTypeIndex != null && this.salePrice > 0)
    },
    doSale() {
      if (this.artSold) {
        return;
      }
      confirmbox('Process Sale?')
        .then(() => {
          var data = 'piece=' + this.piece.id;
          data += '&buyer=' + this.customer.id;
          data += '&price_type=' + encodeURI(this.prices[this.salesTypeIndex].price);
          data += '&price=' + this.salePrice;
          apiRequest('POST', 'artshow/sale/art', data)
            .then((response) => {
              var data = JSON.parse(response.responseText);
              this.alreadySold(data);
              alertbox('Sale Processed');
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
            });
        });
    },
    alreadySold(sales) {
      this.artSold = true;
      this.salePrice = sales.price;
      this.prices.every((value, index) => {
        if (value.price == sales.price_type) {
          this.salesTypeIndex = index;
          return false;
        }
        return true;
      })
      this.buyer = sales.buyer.id;
      this.checkEnableSale();
    },
    checkEnablePrintSale() {
      this.printSaleReady = (!this.printSold && this.print != null && this.customer != null && this.printPrice > 0)
    },
    lookupPrint(origin, item) {
      if (item == null) {
        this.print = null;
        this.printSold = false;
        this.buyer = null;
        this.salesType = null;
        return;
      }
      this.print = item;
      this.debugmsg(JSON.stringify(this.print));
      this.$refs.pnt.displayArt(this.print);
      this.printPrice = this.print['price'];
      this.printQuantity = 1;
      if (parseInt(this.print.sold) >= parseInt(this.print.quantity)) {
        this.printSold = true;
      } else {
        this.printSold = false;
      }
      this.checkEnablePrintSale();
    },
    doPrintSale() {
      if (this.printSold) {
        return;
      }
      confirmbox('Process Sale?')
        .then(() => {
          var data = 'piece=' + this.print.id;
          data += '&buyer=' + this.customer.id;
          data += '&price=' + this.print['price'];
          data += '&quantity=' + this.printQuantity;
          apiRequest('POST', 'artshow/sale/print', data)
            .then(() => {
              alertbox('Sale Processed');
              this.print.sold = parseInt(this.print.sold) + 1;
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
            });
        });
    },
    priceLocked() {
      if (this.salesTypeIndex === null) {
        return false;
      }
      if (!('fixed' in this.prices[this.salesTypeIndex])) {
        return false;
      }
      return this.prices[this.salesTypeIndex].fixed == '1';
    },
    checkPrintQuantity() {
      this.printSold = (parseInt(this.print.sold) + this.printQuantity > parseInt(this.print.quantity));
      this.printPrice = this.print['price'] * this.printQuantity;
      this.checkEnablePrintSale();
    },
    proceedToCheckout() {
      window.location = 'index.php?Function=artshow/checkout&customer=' + this.customer.id;
    }
  }
});

app.component('lookup-user', lookupUser);
app.component('lookup-buyer', lookupBuyer);
app.component('lookup-piece', lookupPiece);
app.component('lookup-print', lookupPrint);
app.component('piece-info', pieceInfo);
app.component('print-info', printInfo);
app.component('find-art', findArt);
app.component('find-print', findPrint);

app.mount('#page');

export default app;
