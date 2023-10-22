/* globals Vue */
const PROPS = {
  staff: Object,
  department: Object
};

const TEMPLATE = `
  <div class="UI-table-cell-no-border">{{staff.departmentName}}</div>
  <div class="UI-table-cell-no-border" v-if="componentIsDept(department).value">{{ staffDivisionName(staff).value }}</div>
  <div class="UI-table-cell-no-border">{{ staffFullName(staff).value }}</div>
  <div class="UI-table-cell-no-border">{{staff.pronouns}}</div>
  <div class="UI-table-cell-no-border">{{ staffPosition(staff, componentIsDept(department).value).value }}</div>
  <div class="UI-table-cell-no-border" v-if="componentIsDept(department).value">{{staff.email}}</div>
  <div class="UI-table-cell-no-border">
    <p>{{staff.note}}</p>
    <p v-if="currentUser?.id === staff.id">This is you!</p>
  </div>
  <div class="UI-table-cell-no-border">
    <button class="UI-redbutton" v-if="canEdit" @click="onEditClicked">Edit</button>
  </div>
`;

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
    isDepartment: this.isDepartment
  };

  this.$emit('editClicked', emittedEventData);
}

const INITIAL_DATA = () => {
  return {
    staffFullName,
    staffDivisionName,
    staffPosition
  }
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
  data: INITIAL_DATA,
  mounted() {
    const departmentId = this.department.id;
    const permissions = this.currentUser.permissions;
    const isDepartment = componentIsDept(this.department);

    const relevantPositions = isDepartment ? this.staffPositions.departmentPositions : this.staffPositions.divisionPositions;
    const currentStaffPosition = relevantPositions.find(position => this.staff.position === position.name);

    this.canEdit = canEditDepartmentStaff(departmentId, permissions, currentStaffPosition);
  },
  methods: {
    onEditClicked,
    componentIsDept
  }
};

export default departmentMemberComponent;
