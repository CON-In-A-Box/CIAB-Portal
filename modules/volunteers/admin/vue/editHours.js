/* jshint esversion: 6 */
/* globals apiRequest, confirmbox, showSidebar, simpleObjectToRequest */

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
      standardModifiers: [
        { value: '0.5', name: 'Half Time'},
        { value: '1.0', name: 'Normal'},
        { value: '1.5', name: 'Time Plus Half'},
        { value: '2.0', name: 'Double'}
      ],
      customModifier: null,
    }
  },
  methods: {
    commitHours() {
      confirmbox(
        'Please! double check entries',
        'Proceed with Volunteer Hour Update?').then(() => {
        const item = {
          hours: parseFloat(this.record.hours),
          end: this.record.end,
          modifier: this.record.modifier,
          department: this.record.department.id,
          authorizer: this.record.authorizer.id
        };

        apiRequest('PUT', '/volunteer/hours/' + this.record.id + '?force=1', simpleObjectToRequest(item))
          .then(() =>  {
            location.reload();
          });
      });
    },
    deleteHours() {
      confirmbox('DELETE Volunteer Entry?').then(() => {
        apiRequest('DELETE', '/volunteer/hours/' + this.record.id)
          .then(() => {
            location.reload();
          });
      });
    },
    show(record) {
      /* deep copy */
      this.record = JSON.parse(JSON.stringify(record));
      record.end = record.end.replace(/\s+/g, 'T');
      var checkModifier = parseFloat(record.modifier);
      var option = this.standardModifiers.find(({ value }) => parseFloat(value) === checkModifier);
      if (!option) {
        this.customModifier = checkModifier.toFixed(1);
      } else {
        this.customModifier = null;
      }

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
          <hour-entry-field v-model="record.hours" id="edit_hours"> </hour-entry-field>
          <label class='UI-label' for='edit_mod'>Time Modifier</label><br />
          <select class="UI-select" style="width:auto" name="TimeModifier" id="edit_mod" v-model="record.modifier">
            <option v-for="v in standardModifiers" :value=v.value>{{v.name}} - 1 hour = {{v.value}}
            hour<span v-if="v.value != 1">s</span> credit</option>
            <option v-if="customModifier" :value=customModifier>Custom Time - 1 hour = {{customModifier}}
            hours credit</option>
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
