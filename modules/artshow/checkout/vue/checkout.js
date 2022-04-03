/* jshint esversion: 6 */
/* globals Vue, apiRequest */

import lookupUser from '../../../../sitesupport/vue/lookupuser.js'
import lookupBuyer from '../../sitesupport/modules/lookupBuyer.js'
import wonArtTable from '../../sitesupport/modules/wonArtTable.js'

var app = Vue.createApp({
  created() {
    apiRequest('GET', 'artshow/', 'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        this.configLoaded();
      });
  },
  mounted() {
    const searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('customer')) {
      this.lookupUser(null, { id: searchParams.get('customer') });
    }
  },
  data() {
    return {
      debug: false,
      debugMessage: null,
      configuration: null,
      customer: null,
      wonCount: 0,
      wonCost: 0,
      wonOwed: 0,
      paid: 0,
      payments: null,
      columns: [{title: 'Payment Date', field: 'date'},
        {title:'Payment Type', field:'payment_type'},
        {title:'Amount', field: 'amount'}],
      paymentType: 'Cash',
      paymentAmount: 0,

    }
  },
  methods: {
    debugmsg(message) {
      if (this.debug) {
        this.debugMessage = message;
      }
    },
    configLoaded() {
      delete this.configuration.paymenttype['type'];
    },
    lookupUser(lookup, user) {
      this.wonCount = 0;
      this.wonCost = 0;
      this.wonOwed = 0;
      this.paid = 0;
      this.customer = user;
      this.payments = null;
      this.debugmsg(JSON.stringify(this.customer));

      this.$refs.won.loadList(this.customer.id)
        .then(() => {
          this.wonCount = this.$refs.won.count();
          this.wonCost = this.$refs.won.getCost();
          this.wonOwed = this.$refs.won.getCost();
          this.paymentAmount = this.wonOwed;
          apiRequest('GET', 'artshow/customer/' + this.customer.id + '/payment', null)
            .then((response) => {
              var data = JSON.parse(response.responseText);
              this.payments = data.data;
              for (var i in this.payments) {
                this.paid += parseFloat(data.data[i].amount);
              }
              this.wonOwed -= this.paid;
              this.paymentAmount = this.wonOwed;
            })
            .catch((response) => {
              if (response instanceof Error) { throw response; }
            });
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    processPayment() {
      var data = 'buyer=' + this.customer.id;
      data += '&payment_type=' + this.paymentType;
      data += '&amount=' + this.paymentAmount;
      apiRequest('POST', 'artshow/customer_payment', data)
        .then(() => {
          this.lookupUser(null, this.customer);
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
        });
    },
    showInvoice() {
      apiRequest('GET', 'artshow/customer/' + this.customer.id + '/invoice', 'format=pdf', true)
        .then((response) => {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        });
    }
  }
});

app.component('lookup-user', lookupUser);
app.component('lookup-buyer', lookupBuyer);
app.component('won-art-table', wonArtTable);

app.mount('#page');

export default app;
