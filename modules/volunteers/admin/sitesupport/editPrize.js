/* jshint esversion: 6 */
/* globals  showSidebar, groupData, confirmbox, basicVolunteersRequestAdmin, groupsNow */

export default {
  data() {
    return {
      title: null,
      record: {
        id: null,
        name: null,
        value: null,
        inventory: null,
        promo: null,
        group: null
      },
    }
  },
  methods: {
    show(record) {
      var groupsNow = JSON.parse(groupData);

      if (!record) {
        this.title = 'Enter New Gift';
      } else {
        this.title = 'Edit Gift Entry';
        /* deep copy */
        this.record = JSON.parse(JSON.stringify(record));
      }

      var grp = document.getElementById('edit_prize_group');
      if (grp.length == 1) {
        var option;
        for (var key in groupsNow) {
          option = document.createElement('option');
          option.text = 'Group #' + key + ' : Limit ' + groupsNow[key];
          option.value = key;
          grp.add(option);
        }
        option = document.createElement('option');
        option.text = 'New Group';
        option.value = 'new';
        grp.add(option);
      }

      if (this.record) {
        if (this.record.reward_group) {
          grp.value = this.record.reward_group.id;
        } else {
          grp.value = 'none';
        }
        this.prizeGroupChange();
      } else {
        grp.value = 'none';
        this.prizeGroupChange();
      }
      showSidebar('edit_prize_div');
    },
    deletePrize() {
      var baseObj = this;
      confirmbox('DELETE Gift Entry?',
        'WARNING!!!<br>  Only do this if NONE of this gift ' +
        'has been distributed. <br>It will lead to corrupt ' +
        'reward records. <br>To DELETE a gift that has been ' +
        'rewarded set inventory to \'0\'').then(
        function() {
          var parameter = 'delete_prize=' + baseObj.record.id;
          basicVolunteersRequestAdmin(parameter, function() {
            location.reload();
          });
        });
    },
    prizeGroupChange() {
      var grp = document.getElementById('edit_prize_group').value;
      var cnt = document.getElementById('edit_prize_group_count');
      if (grp === 'none') {
        cnt.disabled = true;
        cnt.value = '';
        cnt.classList.add('UI-disabled');
      } else if (grp === 'new') {
        cnt.value = 1;
        cnt.disabled = false;
        cnt.classList.remove('UI-disabled');
      } else {
        cnt.value = groupsNow[grp];
        cnt.disabled = false;
        cnt.classList.remove('UI-disabled');
      }
    },
    commitPrize() {
      var message;
      var item = {Name:'', Value:0, RewardGroupID:null, GroupLimit:0,
        Promo:'no', TotalInventory:0, Remaining:0};
      if (this.record.id) {
        message = 'Proceed with Volunteer Gift Update?';
      } else {
        message = 'Proceed with Addition of new Volunteer Gift?';
      }
      var baseObj = this;
      confirmbox('Please! double check entries!', message).then(function() {
        item.Name = baseObj.record.name;
        item.Value = parseFloat(baseObj.record.value);
        var grp = document.getElementById('edit_prize_group').value;
        if (grp === 'none') {
          item.RewardGroupID = '';
        } else {
          item.RewardGroupID = grp;
        }

        item.GroupLimit = parseInt(
          document.getElementById('edit_prize_group_count').value);

        item.Promo = baseObj.record.promo

        if (item.Remaining != baseObj.record.inventory) {
          var amount = parseInt(item.Remaining) - parseInt(baseObj.record.inventory);
          if (amount !== 0) {
            var newValue = parseInt(item.TotalInventory) - amount;
            item.TotalInventory = newValue;
          }
        }
        var parameter;
        if (baseObj.record.id) {
          parameter = 'update_prize=' + JSON.stringify(item);
        } else {
          parameter = 'new_prize=' + JSON.stringify(item);
        }
        console.log(parameter);
        /*
        basicVolunteersRequestAdmin(parameter, function() {
          location.reload();
        });
        */
      });
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
          <input class="UI-input" id="edit_prize_value" v-model="record.value">
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
          <input class="UI-input" id="edit_prize_count" v-model="record.inventory">
                  <label class='UI-label UI-tooltip' for='edit_prize_group'>
                      <span
                        class="VOL-gift-label">
                        If in a group there can be only a limited total number of the items in the group claimed by the volunteer</span>
                  Gift Group:</label>
          <div class="VOL-gift-group-row">
              <div class='VOL-gift-group-select'>
                  <select class="UI-select" id="edit_prize_group"
                   @change="prizeGroupChange">
                    <option value='none'>&lt;none&gt;</option>
                  </select>
              </div>
              <div class='VOL-gift-group-max'>
                  <input class="UI-input UI-disabled" id="edit_prize_group_count" disabled placeholder="max">
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
              Delete
          </button>
          <button id='exit_prize_button' class='UI-redbutton'
              onclick='hideSidebar();'>
             Cancel
          </button>
      </div>
  </div>
  `
}
