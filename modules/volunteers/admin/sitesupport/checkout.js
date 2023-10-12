/* jshint esversion: 6 */
/* globals  apiRequest, showSidebar, hideSidebar, confirmbox, alertbox, showSpinner, hideSpinner */

export default {
  data() {
    return {
      checkout: [],
      hoursSpent: 0,
      groupsNow: [],
      failedList: [],
    }
  },
  methods: {
    processCheckout() {
      confirmbox('Confirm Distribute Gifts',
        'Are the selected gifts correct?').then(() => {
        showSpinner();
        var prizes = [this.checkout.length, 0, 0];
        this.checkout.forEach((item) => {
          console.log(item);
          for (let n = 0; n < item.count; n++) {
            apiRequest('POST', '/volunteer/claims', 'member=' + this.$parent.userId + '&reward=' + item.id)
              .then(() => {
                prizes[1] += 1;
                if (prizes[1] + prizes[2] == prizes[0]) {
                  hideSpinner();
                  document.getElementById('success_dlg').style.display = 'block';
                }
              })
              .catch((response) => {
                const result = JSON.parse(response.responseText);
                if (result.code == 400) {
                  item.failure = result.status;
                } else {
                  item.failure = response.responseText;
                }
                prizes[2] += 1;
                this.failedList.push(item);
                const target = this.checkout.findIndex(element => element.id === item.id);
                if (this.checkout[target].count > 1) {
                  this.checkout[target]['count'] -= 1;
                } else {
                  this.checkout.splice(target, 1);
                }
                if (prizes[1] + prizes[2] == prizes[0]) {
                  hideSpinner();
                  document.getElementById('success_dlg').style.display = 'block';
                }
              });
          }
        });
      });
    },
    clearCheckout() {
      hideSidebar();
      this.hoursSpent = 0;
      this.checkout = [];
      this.groupsNow = [];
    },
    updateCost(cost) {
      this.hoursSpent += cost;
    },
    addToCheckout(item) {
      this.init();
      var cost = parseFloat(item.value);
      var found = false;
      var target = null;
      this.checkout.every((element) => {
        if (element.id == item.id) {
          found = true;
          target = element;
          return false;
        }
        return true;
      });

      if (item.promo == '1') {
        if (found) {
          alertbox('Promo item \'' + item.name + '\' cannot be added twice');
          return;
        }
        cost = 0;
      }
      if (this.$parent.$refs.gfttbl.hoursRemaining < this.hoursSpent + cost) {
        alertbox('Volunteer does not have enough hours for the ' + item.name);
        return;
      }
      if (item.reward_group !=  null) {
        if (!(item.reward_group.id in this.groupsNow)) {
          this.groupsNow[item.reward_group.id] = 0;
        } else {
          if (this.groupsNow[item.reward_group.id] + 1 > item.reward_group.reward_limit) {
            alertbox('Too many items from limited group');
            return;
          }
        }
      }
      var count = 1;
      if (target != null) {
        count += target.count;
      }

      if (item.claimed + count > item.inventory) {
        alertbox('Not enough items in inventory!');
        return;
      }

      if (target == null) {
        item.count = count;
        this.checkout.push(item);
      } else {
        target.count = count;
      }

      this.updateCost(cost);

      if (item.reward_group !== null) {
        this.groupsNow[item.reward_group.id] += 1;
      }

      this.show();
    },
    addPromoToCheckout(unclaimed) {
      unclaimed.forEach((item) => {
        if (item.reward_group == null) {
          this.addToCheckout(item);
        }
      });
    },
    removeFromCheckout(index, item) {
      this.updateCost(-1 * parseFloat(item.value));
      if (item.count > 1) {
        item.count -= 1;
      } else {
        this.checkout.splice(index, 1);
      }
      if (item.reward_group !== null) {
        this.groupsNow[item.reward_group.id] -= 1;
      }
    },
    init() {
      if (this.groupsNow.length == 0) {
        this.$parent.$refs.gfttbl.groups.forEach((entry, index) => {
          this.groupsNow[index] = entry.acquired;
        });
      }
    },
    show() {
      this.init();
      showSidebar('checkout_div');
    },
    printHoursLeft() {
      if (this.$parent.$refs.gfttbl) {
        return this.$parent.printHours(this.$parent.$refs.gfttbl.hoursRemaining - this.hoursSpent);
      }
    },
    printHoursSpent() {
      return this.$parent.printHours(this.hoursSpent);
    }
  },
  template: `
  <div class='UI-sidebar-hidden' id='checkout_div'>
    <div class='UI-center'>
        <h2>Items claimed now</h2>
    </div>
    <div class='UI-stripedtable'>
      <div v-for="(c, i) in checkout" class="VOL-hover-red UI-table-row" @click="removeFromCheckout(i, c)">
        <div class="UI-table-cell">{{c.name}}<span v-if="c.count > 1"> x{{c.count}}</span></div>
      </div>
    </div>
    <div class="VOL-claim-hour-div">
        <span>Hours Left: {{printHoursLeft()}}</span>
        <br>
        <span>Hours Spent: {{printHoursSpent()}}</span>
    </div>
    <div class='UI-center'>
        <button id='checkout_button' class='UI-eventbutton'
            @click='processCheckout'>
            Distribute Items
        </button>
        <button id='clear_button' class='UI-redbutton'
            @click='clearCheckout'>
           Clear
        </button>
    </div>
  </div>

  <div id="success_dlg" class="UI-modal">
    <div class="UI-modal-content">
      <div class="UI-container">
        <span onclick="document.getElementById('success_dlg').style.display='none';
          location.reload();"
        class="VOL-give-close">&times;</span>
        <div v-if="failedList.length > 0">
          <h2 class="UI-red">The following gifts failed to distribute!
                             Do not give them to the volunteer!</h2>
            <div class='UI-stripedtable'>
              <div v-for="(c, i) in failedList" class="UI-table-row">
                <div class="UI-table-cell">{{c.name}}</div>
                <div class="UI-table-cell">{{c.failure}}</div>
              </div>
            </div>
          <hr>
        </div>
        <div v-if="checkout && checkout.length > 0">
          <h2 class="UI-center event-color-primary">Please get the volunteer the following new gifts!</h2>
          <hr>
          <div class='UI-stripedtable'>
            <div v-for="(c, i) in checkout" class="UI-table-row">
              <div class="UI-table-cell">{{c.name}}<span v-if="c.count > 1"> x{{c.count}}</span></div>
            </div>
          </div>
        </div>
        <h2 class="UI-center UI-red">Close this window when all gifts have been claimed!</h2>
      </div>
    </div>
  </div>
  `
}
