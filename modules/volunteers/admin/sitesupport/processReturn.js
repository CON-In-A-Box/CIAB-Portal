/* jshint esversion: 6 */
/* globals  showSidebar, confirmbox, basicVolunteersRequestAdmin */

export default {
  data() {
    return {
      claims: null,
      creditHours: 0,
    }
  },
  methods: {
    processReturn() {
      var objRef = this;
      confirmbox('Confirm Gift Return',
        'Are the returned gifts correct?').then(function() {
        var data = [];
        objRef.claims.forEach((c) => {
          if (c.returned) {
            data.push(c.reward.id);
          }
        });
        var parameter = 'refundId=' + this.$parent.userId + '&rewards=' + JSON.stringify(data);
        basicVolunteersRequestAdmin(parameter, function() {
          document.getElementById('return_success_dlg').style.display = 'block';
        });
      });
    },
    markDelete(index, claim) {
      claim.returned = !claim.returned;
      var total = 0;
      this.claims.forEach((c) => {
        if (c.returned && c.reward.promo != '1') {
          total += parseFloat(c.reward.value);
        }
      });

      this.creditHours = Math.round(total * 100) / 100;
    },
    show(claims) {
      this.claims = JSON.parse(JSON.stringify(claims));
      this.claims.sort((a,b) => (a.reward.name > b.reward.name) ? 1 : -1);
      showSidebar('return_div');
    },
  },
  template: `
    <div class='UI-sidebar-hidden' id='return_div'>
      <div class='UI-center'>
          <h2>Items being returned now:</h2>
      </div>
      <div class='UI-stripedtable'>
        <div v-for="(c, i) in claims" :class="[{'UI-yellow': c.returned},'UI-table-row','VOL-hover-red']" @click="markDelete(i, c)">
          <div class="UI-table-cell">{{c.reward.name}}</div>
          <div v-if="c.reward.promo=='0'" class="UI-table-cell">{{$parent.printHours(c.reward.value)}}</div>
          <div v-else class="UI-table-cell">Free</div>
        </div>
      </div>
      <div class="VOL-return-hours">
          <span>Hours To Be Credited: <span>{{$parent.printHours(creditHours)}}</span></span>
      </div>
      <div class='UI-center'>
          <button id='return_button' class='UI-eventbutton' @click='processReturn'>
              Return Items
          </button>
          <button id='return_clear_button' class='UI-redbutton' onclick='hideSidebar();'>
             Cancel
          </button>
      </div>

      <div id="return_success_dlg" class="UI-modal">
        <div class="UI-modal-content">
          <div class="UI-container">
            <span onclick="document.getElementById('return_success_dlg').style.display='none';
              location.reload();"
            class="VOL-return-label">&times;</span>
            <h2 class="UI-center event-color-primary">Please collect and return the following gifts</h2>
            <hr>
            <div class='UI-stripedtable'>
              <div v-for="(c, i) in claims" class="UI-table-row">
                <div v-if="c.returned" class="UI-table-cell">{{c.reward.name}}</div>
               </div>
            </div>
            <h2 class="UI-center UI-red">Close this window when all returns have been processed!</h2>
          </div>
        </div>
      </div>
  </div>
  `
}
