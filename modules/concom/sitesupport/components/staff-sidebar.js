/* globals Vue, apiRequest */
const PROPS = {
  department: Object,
  isDepartment: Boolean
};

const DIRECTOR_POSITION_ID = 1;

const TEMPLATE = `
  <div class="UI-sidebar-shown UI-fixed">
    <div class="UI-center">
      <h2 class="UI-red">Add to {{ getHeaderText(isDepartment).value }}</h2>
    </div>
    <div class="UI-center">
      <label class="UI-label" for="concom_lookup">
        <h2>Add someone to {{ department.name }}</h2>
      </label>
      <fieldset>
        <legend>Search the User Database</legend>
        <div class="UI-rest UI-center" id="concom_lookup">
          <lookup-user @handler="onUserLookup"></lookup-user>
        </div>
      </fieldset>
      <p>
        <label class="UI-label" for="position">Position:</label>
        <select v-model="selectedPosition">
          <option disabled value="">Please select a position</option>
          <option v-for="position in availablePositions(staffPositions, isDepartment).value" :key=position.id :value=position>
            {{position.name}}
          </option>
        </select>
      </p>
    </div>
    <div class="UI-center UI-padding">
      <button class="UI-eventbutton" :disabled="disableForm(userId, selectedPosition).value" @click="onSubmit">OK</button>
      <button class="UI-redbutton" @click="$emit('sidebarClosed')">Close</button>
    </div>
  </div>
`;

const availablePositions = (staffPositions, isDepartment) => Vue.computed(() => {
  if (isDepartment) {
    return staffPositions.departmentPositions
  }

  return staffPositions.divisionPositions.map(position => {
    return {
      id: position.id,
      name: position.id === DIRECTOR_POSITION_ID ? 'Director' : 'Support'
    }
  })
});

const getHeaderText = (isDepartment) => Vue.computed(() => {
  return isDepartment ? 'Department' : 'Division';
});

const disableForm = (userId, position) => Vue.computed(() => {
  return userId === null || (position === '' || position?.id === null);
});

function onUserLookup(_, item) {
  this.userId = item.Id;
}

async function onSubmit() {
  const postData = {
    Department: this.department.id,
    Position: this.selectedPosition.id
  };

  const staffUpdateResponse = await apiRequest('POST', `member/${this.userId}/staff_membership`,
    `Department=${postData.Department}&Position=${postData.Position}`);

  if (staffUpdateResponse.status === 201) {
    const staffResponse = await apiRequest('GET', `member/${this.userId}`);
    const staff = JSON.parse(staffResponse.responseText);
    const emittedEventData = {
      staff: {
        departmentName: this.department.name,
        firstName: staff.first_name,
        lastName: staff.last_name,
        pronouns: staff.pronouns,
        position: this.selectedPosition.name,
        email: staff.email
      },
      departmentId: this.department.id,
      eventType: 'added'
    };

    this.$emit('sidebarFormSubmitted', emittedEventData);
  }
}

const INITIAL_DATA = () => {
  return {
    userId: null,
    selectedPosition: Vue.ref('')
  }
};

function componentSetup() {
  const staffPositions = Vue.inject('staffPositions');

  return {
    staffPositions
  }
}

const staffSidebarComponent = {
  props: PROPS,
  emits: ['sidebarClosed', 'sidebarFormSubmitted'],
  data: INITIAL_DATA,
  template: TEMPLATE,
  setup: componentSetup,
  methods: {
    onUserLookup,
    onSubmit,
    availablePositions,
    disableForm,
    getHeaderText
  }
};

export default staffSidebarComponent;
