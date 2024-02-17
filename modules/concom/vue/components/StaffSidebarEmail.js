const PROPS = {
  data: Object,
  edit: Boolean
}

const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Position Email</h2>
  </div>
  <div>
    <hr/>
    <label class="UI-label" for="updated_email_email">EMail:</label>
    <input id="updated_email_email" class="UI-input" value="test@test-con.org" />
    <input id="updated_email_original" class="UI-hide" readonly />
    <input id="updated_email_alias" class="UI-hide" readonly />
    <input id="updated_email_index" class="UI-hide" readonly />
    <input id="updated_email_dept" class="UI-hide" readonly />
  </div>
  <div class="UI-center">
    <hr/>
    <button class="UI-eventbutton">Save</button>
    <button class="UI-yellowbutton" @click="$emit('closeClicked')">Close</button>
    <button class="UI-redbutton">Delete</button>
  </div>
`;

const StaffSidebarEmail = {
  props: PROPS,
  emits: [ 'closeClicked' ],
  template: TEMPLATE
};

export default StaffSidebarEmail;
