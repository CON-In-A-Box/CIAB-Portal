/* globals Vue, confirmbox */
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
    <label class="UI-label" for="email">EMail:</label>
    <input id="email" class="UI-input" v-model="email" />
  </div>
  <div class="UI-center">
    <hr/>
    <button class="UI-eventbutton" @click="onSave">Save</button>
    <button class="UI-yellowbutton" @click="$emit('closeClicked')">Close</button>
    <button class="UI-redbutton" :disabled="emailId === -1" @click="onDelete">Delete</button>
  </div>
`;

async function onSave() {
  try {
    const confirmBoxTitle = 'Confirms Save Email';
    const confirmBoxMessage = `Really save the e-mail address "${this.email}"?`;

    await confirmbox(confirmBoxTitle, confirmBoxMessage);

    const data = {
      id: this.emailId,
      email: this.email,
      departmentId: this.departmentId
    };

    this.$emit('saveEmailClicked', data);
  } catch (error) {
    // User canceled.
  }
}

async function onDelete() {
  try {
    const confirmBoxTitle = 'Confirms Email Deletion';
    const confirmBoxMessage = `Really delete the e-mail address "${this.email}"?`;
    await confirmbox(confirmBoxTitle, confirmBoxMessage);

    const data = {
      id: this.emailId
    };

    this.$emit('deleteEmailClicked', data);
  } catch (error) {
    // User canceled.
  }
}

function setup(props) {
  const emailId = Vue.ref(props.data?.email ? props.data.email.id : -1);
  const email = Vue.ref(props.data?.email ? props.data.email.email : '');
  const departmentId = props.data?.email ? props.data.email.departmentId : props.data.departmentId;

  return {
    emailId,
    email,
    departmentId
  }
}

const StaffSidebarEmail = {
  props: PROPS,
  emits: ['closeClicked', 'saveEmailClicked', 'deleteEmailClicked'],
  setup,
  methods: {
    onSave,
    onDelete
  },
  template: TEMPLATE
};

export default StaffSidebarEmail;
