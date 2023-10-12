/* globals Vue, apiRequest */
const DIRECTOR_POSITION_ID = 1;

const TEMPLATE = `
  <div class="UI-sidebar-shown UI-fixed">
    <div class="UI-center">
      <h2 class="UI-red">{{ getHeaderText(isEdit, isDepartment).value }}</h2>
    </div>
    <div class="UI-center" v-if="!isEdit">
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
    <template v-if="isEdit">
      <div>
        <h3 class="UI-center">{{ getStaffFullName(editStaff).value }}</h3>
        <h4 class="UI-center">{{ getStaffPosition(editStaff, isDepartment).value }} in {{ department.name }}</h4>
        <label class="UI-label" for="user_notes">Note</label>
        <input class="UI-input" id="user_notes" v-model="staffNote"/>
        <br/>
        <label class="UI-label" for="user_pos">Position: </label>
        <select v-model="selectedPosition">
          <option v-for="position in availablePositions(staffPositions, isDepartment).value" :key=position.id :value=position>
            {{position.name}}
          </option>
        </select>
      </div>
    </template>
    <div class="UI-center UI-padding">
      <button class="UI-eventbutton" :disabled="disableForm(userId, selectedPosition).value" @click="onSubmit">OK</button>
      <button class="UI-redbutton" v-if="isEdit" @click="onRemoveStaff">Remove</button>
      <button :class="getCloseButtonClass(isEdit).value" @click="$emit('sidebarClosed')">Close</button>
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

const getCloseButtonClass = (isEdit) => Vue.computed(() => {
  return isEdit ? 'UI-yellowbutton' : 'UI-redbutton'
});

const getHeaderText = (isEdit, isDepartment) => Vue.computed(() => {
  if (isEdit) {
    return 'Modify Membership';
  }

  const deptText = isDepartment ? 'Department' : 'Division';
  return `Add to ${deptText}`;
});

const getStaffFullName = (staff) => Vue.computed(() => {
  return `${staff.firstName} ${staff.lastName}`;
});

const getStaffPosition = (staff, isDepartment) => Vue.computed(() => {
  if (!isDepartment) {
    if (staff.position === 'Head') {
      return 'Director';
    } else if (staff.position === 'Specialist') {
      return 'Support';
    }
  }

  return staff.position;
});

const disableForm = (userId, position) => Vue.computed(() => {
  return userId === null || (position === '' || position?.id === null);
});

function onUserLookup(_, item) {
  this.userId = item.Id;
}

async function onSubmit() {
  if (this.isEdit) {
    await editStaff(this);
  } else {
    await addStaff(this);
  }
}

async function editStaff(component) {
  const putData = {
    Department: component.department.id,
    Position: component.selectedPosition.id,
    Note: component.staffNote
  };

  const staffUpdateResponse = await apiRequest('PUT', `member/${component.userId}/staff_membership`,
    `Department=${putData.Department}&Position=${putData.Position}&Note=${putData.Note}`);

  if (staffUpdateResponse.status === 200) {
    const emittedEventData = {
      staff: {
        ...component.editStaff,
        position: component.selectedPosition.name,
        note: component.staffNote
      },
      eventType: 'updated'
    };

    component.$emit('sidebarFormSubmitted', emittedEventData);
  }
}

async function addStaff(component) {
  const postData = {
    Department: component.department.id,
    Position: component.selectedPosition.id
  };

  const staffAddResponse = await apiRequest('POST', `member/${component.userId}/staff_membership`,
    `Department=${postData.Department}&Position=${postData.Position}`);

  if (staffAddResponse.status === 201) {
    const staffData = JSON.parse(staffAddResponse.responseText);
    if (staffData) {
      const emittedEventData = {
        staff: {
          id: component.userId,
          deptStaffId: parseInt(staffData.id),
          departmentName: component.department.name,
          divisionName: component.division.name,
          firstName: staffData.member.first_name,
          lastName: staffData.member.last_name,
          pronouns: staffData.member.pronouns,
          position: component.selectedPosition.name,
          email: staffData.member.email,
          note: staffData.member.note
        },
        departmentId: component.department.id,
        eventType: 'added'
      }

      component.$emit('sidebarFormSubmitted', emittedEventData);
    }
  }
}

async function onRemoveStaff() {
  const staffDeleteResponse = await apiRequest('DELETE', `staff/membership/${this.deptStaffId}`);

  if (staffDeleteResponse.status === 204) {
    const emittedEventData = {
      staff: this.editStaff,
      department: this.department,
      eventType: 'removed'
    };

    this.$emit('sidebarFormSubmitted', emittedEventData);
  }
}

const INITIAL_DATA = () => {
  return {
    userId: null,
    deptStaffId: null,
    selectedPosition: Vue.ref('')
  }
};

function componentSetup() {
  const staffPositions = Vue.inject('staffPositions');
  const staffNote = Vue.ref('');
  const isEdit = Vue.ref(false);

  const department = Vue.inject('sidebarDept');
  const division = Vue.inject('sidebarDivision');
  const departmentStaff = Vue.inject('sidebarDeptStaff');
  const isDepartment = Vue.inject('sidebarDeptIsDepartment');
  const editStaff = Vue.inject('sidebarEditStaff');

  return {
    staffPositions,
    staffNote,
    isEdit,
    department,
    division,
    departmentStaff,
    isDepartment,
    editStaff
  }
}

const staffSidebarComponent = {
  emits: ['sidebarClosed', 'sidebarFormSubmitted'],
  data: INITIAL_DATA,
  template: TEMPLATE,
  setup: componentSetup,
  mounted() {
    if (this.editStaff.id != null) {
      this.userId = this.editStaff.id;
      this.deptStaffId = this.editStaff.deptStaffId;
      this.staffNote = this.editStaff.note;
      this.isEdit = true;

      const availablePositions = this.availablePositions(this.staffPositions, this.isDepartment).value;
      let positionToFind = this.editStaff.position;

      if (!this.isDepartment) {
        if (this.editStaff.position === 'Head') {
          positionToFind = 'Director';
        } else if (this.editStaff.position === 'Specialist') {
          positionToFind = 'Support';
        }
      }

      const foundPosition = availablePositions.find(position => position.name === positionToFind);
      this.selectedPosition = foundPosition;
    }
  },
  methods: {
    onUserLookup,
    onSubmit,
    editStaff,
    addStaff,
    onRemoveStaff,
    availablePositions,
    disableForm,
    getHeaderText,
    getStaffFullName,
    getStaffPosition,
    getCloseButtonClass
  }
};

export default staffSidebarComponent;