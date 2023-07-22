/* jshint esversion: 6 */
/* globals confirmbox, basicVolunteersRequestAdmin, showSidebar */

export default {
  data() {
    return {
      record: {
        hours: 0,
        member: {
          first_name: '',
        },
        enterer: {
          id: '',
        },
        authorizer: {
          id: '',
        },
        department: {
          name: '',
        },
        end: null,
      },
    }
  },
  methods: {
    commitHours() {
      var baseObj = this;
      confirmbox(
        'Please! double check entries',
        'Proceed with Volunteer Hour Update?').then(function() {
        var item = {};
        item['EntryID'] = baseObj.record.id;
        item['Actual Hours'] = parseFloat(baseObj.record.hours);
        item['End Date Time'] = baseObj.record.end;
        var e = document.getElementById('edit_mod');
        item['Time Modifier'] = e.options[e.selectedIndex].value;
        item['Department Worked'] = baseObj.record.department.name;
        item['Authorized By'] = baseObj.record.authorizer.id;

        var parameter = 'update_hour=' + JSON.stringify(item);
        basicVolunteersRequestAdmin(parameter, function() {
          location.reload();
        });
      });
    },
    deleteHours() {
      var baseObj = this;
      confirmbox('DELETE Volunteer Entry?').then(function() {
        console.log(baseObj.record);
        var parameter = 'delete_hour=' + baseObj.record.id;
        basicVolunteersRequestAdmin(parameter, function() {
          location.reload();
        });
      });
    },
    show(record) {
      /* deep copy */
      this.record = JSON.parse(JSON.stringify(record));

      var options = document.getElementById('edit_mod');
      var value = parseFloat(record.modifier);
      options.selectedIndex = 0;
      for (var i = 0, n = options.length; i < n ; i++) {
        if (options[i].value == value) {
          options.selectedIndex = i;
          break;
        } else if (options[i].value < value) {
          options.selectedIndex = i;
        } else {
          break;
        }
      }

      record.end = record.end.replace(/\s+/g, 'T');
      showSidebar('edit_user_hour_div');
    },
  },
  template: `
    <div class='UI-sidebar-hidden' id='edit_user_hour_div'>
      <div class='UI-center UI-red'>
          <h2>Edit Hours Entry</h2>
      </div>
      <div class='UI-border UI-padding'>
        <form>
          <label class='UI-label' for='edit_name'>Volunteer:</label>
          <input class="UI-input UI-disabled" v-model="record.member.first_name" readonly>
          <label class='UI-label' for='edit_hours'>Actual Hours:</label>
          <input class="UI-input" id="edit_hours" v-model="record.hours">
          <label class='UI-label' for='edit_mod'>Time Modifier</label><br />
          <select class="UI-select" style="width:auto" name="TimeModifier" id="edit_mod">
            <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
            <option value=1 selected>Normal - 1 hour = 1 hour</option>
            <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
            <option value=2>Double - 1 hour = 2 hours credit</option>
          </select>
          <label class='UI-label' for='edit_end'>End Time:</label>
          <input class="UI-input" type="datetime-local" id="edit_end" pattern="d{4}-d{2}-d{2}Td{2}:d{2}" v-model="record.end">
          <label class='UI-label' for='edit_dept'>Department</label><br />
          <department-dropdown :name-as-value="true" id="edit_dept" v-model="record.department.name"> </department-dropdown>
          <label class='UI-label' for='edit_enter'>Entered By:</label>
          <input class="UI-input UI-disabled" id="edit_enter" readonly v-model="record.enterer.id">
          <label class='UI-label' for='edit_auth'>Authorized By:</label>
          <input class="UI-input" id="edit_auth" v-model="record.authorizer.id">
        </form>
      </div>
      <div class='UI-center'>
          <button id='set_hours_button' class='UI-eventbutton' @click='commitHours'>
              Commit
          </button>
          <button id='delete_hours_button' class='UI-secondarybutton' @click='deleteHours'>
              Delete
          </button>
          <button id='exit_hours_button' class='UI-redbutton' onclick='hideSidebar();'>
             Cancel
          </button>
      </div>
  </div>
  `
}
