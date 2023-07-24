/* jshint esversion: 6 */
/* globals  showSidebar, hideSidebar, confirmbox, basicVolunteersRequestAdmin, alertbox */

export default {
  data() {
    return {
      checkout: [],
      hoursSpent: 0,
      groupsNow: [],
    }
  },
  methods: {
    processCheckout() {
      var baseObj = this;
      confirmbox('Confirm Distribute Gifts',
        'Are the selected gifts correct?').then(function() {
        var prizes = [];
        baseObj.checkout.forEach((item) => {
          prizes.push({'PrizeID' : item.id});
        });
        var parameter = 'rewardId=' + baseObj.$parent.userId + '&rewards=' +
          JSON.stringify(prizes);
        basicVolunteersRequestAdmin(parameter, function() {
          document.getElementById('success_dlg').style.display = 'block';
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
        this.$parent.$refs.gfttbl.groupCount.forEach((entry, index) => {
          this.groupsNow[index] = entry;
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
        <h2 class="UI-center event-color-primary">Please get the volunteer the following new gifts!</h2>
        <hr>
        <table class='VOL-give-gift-table' id='reward_list'>
        </table>
        <h2 class="UI-center UI-red">Close this window when all gifts have been claimed!</h2>
      </div>
    </div>
  </div>
  `
}
