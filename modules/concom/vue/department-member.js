/* globals Vue */
const PROPS = {
  staff: Object,
  department: Object,
  isSpecialDivision: Boolean
};

const TEMPLATE = `
  <template v-if="isSpecialDivision">
    <div class="CONCOM-list-special-division-staff-column">{{ staff.departmentName }}</div>
    <div class="CONCOM-list-special-division-staff-column">{{ staffFullName(staff).value }}</div>
    <div class="CONCOM-list-special-division-staff-column">{{ staff.pronouns }}</div>
    <div class="CONCOM-list-special-division-staff-column">{{ staff.email }}</div>
    <div class="CONCOM-list-special-division-staff-column">
      <p>{{staff.note}}</p>
      <p v-if="currentUser?.id === staff.id">This is you!</p>
    </div>
    <div class="CONCOM-list-edit-column">
      <button class="CONCOM-edit-member-button" v-if="canEdit" @click="onEditClicked">Edit</button>
    </div>
  </template>
  <template v-else>
    <div :class="columnClass(department).value">{{ staff.departmentName }}</div>
    <div :class="columnClass(department).value" v-if="componentIsDept(department).value">{{ staffDivisionName(staff).value }}</div>
    <div :class="columnClass(department).value">{{ staffFullName(staff).value }}</div>
    <div :class="columnClass(department).value">{{ staff.pronouns }}</div>
    <div :class="columnClass(department).value">{{ staffPosition(staff, componentIsDept(department).value).value }}</div>
    <div :class="columnClass(department).value" v-if="componentIsDept(department).value">{{ staff.email }}</div>
    <div :class="columnClass(department).value">
      <p>{{staff.note}}</p>
      <p v-if="currentUser?.id === staff.id">This is you!</p>
    </div>
    <div class="CONCOM-list-edit-column">
      <button class="CONCOM-edit-member-button" v-if="canEdit" @click="onEditClicked">Edit</button>
    </div>
  </template>
`;

const columnClass = (department) => Vue.computed(() => {
  return componentIsDept(department).value ? 'CONCOM-list-department-staff-column' : 'CONCOM-list-division-staff-column';
})

const staffFullName = (staff) => Vue.computed(() => {
  return `${staff.firstName} ${staff.lastName}`;
});

const staffDivisionName = (staff) => Vue.computed(() => {
  return staff.divisionName ?? staff.departmentName;
});

const staffPosition = (staff, isDepartment) => Vue.computed(() => {
  if (!isDepartment) {
    if (staff.position === 'Head') {
      return 'Director';
    } else if (staff.position === 'Specialist') {
      return 'Support';
    }
  }

  return staff.position;
});

const componentIsDept = (department) => Vue.computed(() => {
  return department.parentId !== undefined;
});

const canEditDepartmentStaff = (departmentId, permissions, staffPosition) => {
  const editAny = permissions.find((permission) => permission.subtype === 'api.put.staff.all')?.allowed === 1;
  const editStaff = permissions.find((permission) => permission.subtype === `api.put.staff.${departmentId}.${staffPosition.id}`)?.allowed === 1;

  return editAny || editStaff;
}

function onEditClicked() {
  const emittedEventData = {
    staff: this.staff,
    isDepartment: this.componentIsDept(this.department).value
  };

  this.$emit('editClicked', emittedEventData);
}

const departmentMemberComponent = {
  props: PROPS,
  template: TEMPLATE,
  emits: [ 'editClicked' ],
  setup() {
    const currentUser = Vue.inject('currentUser');
    const staffPositions = Vue.inject('staffPositions');
    const canEdit = Vue.ref(false);
    return {
      currentUser,
      staffPositions,
      canEdit
    }
  },
  mounted() {
    const departmentId = this.department.id;
    const permissions = this.currentUser.permissions;
    const isDepartment = componentIsDept(this.department);

    const relevantPositions = isDepartment ? this.staffPositions.departmentPositions : this.staffPositions.divisionPositions;
    const currentStaffPosition = relevantPositions.find(position => this.staff.position === position.name);

    this.canEdit = canEditDepartmentStaff(departmentId, permissions, currentStaffPosition);
  },
  methods: {
    staffFullName,
    staffDivisionName,
    staffPosition,
    columnClass,
    onEditClicked,
    componentIsDept
  }
};

export default departmentMemberComponent;
