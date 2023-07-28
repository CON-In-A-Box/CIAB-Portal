/* jshint esversion: 6 */
/* globals  apiRequest, showSidebar, confirmbox, simpleObjectToRequest */

export default {
  data() {
    return {
      title: null,
      record: {
        id: null,
        name: null,
        value: null,
        inventory: null,
        remaining: null,
        promo: null,
        group: null,
      },
      reward_groups: [],
      reward_group: -1,
      reward_limit: null,
    }
  },
  methods: {
    show(record) {
      if (!record) {
        this.title = 'Enter New Gift';
      } else {
        this.title = 'Edit Gift Entry';
        /* deep copy */
        this.record = JSON.parse(JSON.stringify(record));
      }

      this.reward_groups = this.$parent.reward_groups;

      if (this.record) {
        if (this.record.reward_group) {
          this.reward_group = this.record.reward_group.id;
          this.reward_limit = this.record.reward_group.reward_limit;
        } else {
          this.reward_group = -1;
        }
        this.record.remaining = this.record.inventory - this.record.claimed;
      } else {
        this.reward_group = -1;
      }
      showSidebar('edit_prize_div');
    },
    deletePrize() {
      let warning = `
        <span class='UI-bold'>WARNING!!!</span><br> NONE of this gift has been distributed.<br>
        Deleting the gift will remove all record from the system and it will not be able to be recovered.
      `;
      let title = 'DELETE Gift Entry?';
      if (this.record.claimed) {
        warning = `
        <span class='UI-bold'>WARNING!!!</span><br>
        This gift will have the inventory set to zero and be removed from the system interface.<br>
        It will no longer be able to be selected for distribution, however records of previous distributions will remain.
        `;
        title = 'Retire Gift Entry?';
      }
      confirmbox(title, warning).then(() => {
        apiRequest('DELETE', '/volunteer/rewards/' + this.record.id).
          then(() => {
            location.reload();
          });
      });
    },
    commitPrize() {
      var message;
      var method;
      var tail = '';
      var item = {
        name: this.record.name,
        reward_group: this.reward_group,
        promo: this.record.promo,
        inventory: this.record.remaining,
        value: this.record.value
      };
      if (this.record.id) {
        message = 'Proceed with volunteer gift update?';
        method = 'PUT';
        tail = '/' + this.record.id;
        item.id = this.record.id;
      } else {
        message = 'Proceed with the addition of new volunteer gift?';
        method = 'POST';
      }
      confirmbox('Please! double check entries!', message).then(() => {
        apiRequest(method, '/volunteer/rewards' + tail, simpleObjectToRequest(item))
          .then(() => { location.reload();
          });
      });
    },
    updateGroup() {
      var found = false;
      this.reward_groups.every((entry) => {
        if (entry.id == this.reward_group) {
          found = true;
          this.reward_limit = entry.reward_limit;
          return false;
        }
        return true;
      });
      if (!found) {
        this.reward_limit = -1;
      }
    },
  },
  template: `
  <div class='UI-sidebar-hidden' id='edit_prize_div'>
      <div class='UI-center UI-red'>
          <h2 id='edit_prize_title'>{{title}}</h2>
      </div>
      <div class='UI-border UI-padding'>
        <form>
          <label class='UI-label UI-tooltip' for='edit_prize_name'>
              <span
                class="VOL-gift-label">Visible name of the item</span>
          Name:</label>
          <input class="UI-input" id="edit_prize_name" placeholder="<name>" v-model="record.name">
          <label class='UI-label UI-tooltip' for='edit_prize_value'>
              <span
                class="VOL-gift-label">Hour cost of the item</span>
          Value:</label>
          <hour-entry-field v-model="record.value" id="edit_prize_value"> </hour-entry-field>
          <label class='UI-label UI-tooltip' for='edit_prize_value'>
              <span
                class="VOL-gift-label">
                If 'yes' this item does not cost hours but instead is free at the given hour mark. Also enforces a limit of 1.</span>
          Promo Item:</label>
          <select class="UI-select" v-model="record.promo">
            <option value='1'>Yes</option>
            <option value='0'>No</option>
          </select>
          <label class='UI-label UI-tooltip' for='edit_prize_count'>
              <span
                class="VOL-gift-label">Changing this will adjust Total items</span>
          Inventory Remaining:</label>
          <input class="UI-input" id="edit_prize_count" v-model="record.remaining">
                  <label class='UI-label UI-tooltip' for='edit_prize_group'>
                      <span
                        class="VOL-gift-label">
                        If in a group there can be only a limited total number of the items in the group claimed by the volunteer</span>
                  Gift Group:</label>
          <div class="VOL-gift-group-row">
              <div class='VOL-gift-group-select'>
                  <select class="UI-select" id="edit_prize_group" v-model="reward_group" @change="updateGroup">
                    <option value='-1'>&lt;none&gt;</option>
                    <option v-for="g in reward_groups" :value="g.id">
                      Group #{{g.id}}
                    </option>
                  </select>
              </div>
              <div v-if="reward_group != -1" class='VOL-gift-group-max'>
                  <label class='UI-label VOL-gift-group-max-label' for='edit_prize_group_count'>Limit:</label>
                  <input class="VOL-gift-group-max-input" id="edit_prize_group_count" placeholder="max"
                  v-model="reward_limit">
              </div>
          </div>

          <input class="UI-input UI-disabled UI-hide" id="prize_data" readonly>
        </form>
      </div>
      <div class='UI-center'>
          <button class='UI-eventbutton'
              @click='commitPrize'>
              Commit
          </button>
          <button v-if="record.id != null" class='UI-secondarybutton'
              @click='deletePrize'>
              <span v-if="record.claimed==0">Delete</span>
              <span v-else>Retire</span>
          </button>
          <button id='exit_prize_button' class='UI-redbutton'
              onclick='hideSidebar();'>
             Cancel
          </button>
      </div>
  </div>
  `
}
