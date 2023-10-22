/* globals Vue */
const PROPS = {
  department: Object,
  division: Object,
  divisionStaffMap: Object
}

const TEMPLATE = `
  <div class="CONCOM-list-department-separator" v-if="isDepartment(department).value">
    <div class="CONCOM-list-department-separator-row">
      <div class="CONCOM-list-separator-row-column">{{department.name}}</div>
      <div class="CONCOM-list-separator-row-column">
        <a :href="divisionNavigationRef(division).value">{{division.name}}</a>
      </div>
      <div class="CONCOM-list-separator-row-column">
        <template v-for="email in department.email">{{email}}<br/></template>
      </div>
    </div>
  </div>
  <div :class="tableClass(department).value">
    <department-header :isDepartment=isDepartment(department).value :department=department></department-header>
    <div class="CONCOM-list-staff-container" v-for="staff in departmentStaff">
      <department-member :staff=staff :department=department @edit-clicked="editStaffClicked"></department-member>
    </div>
  </div>
  <div class="CONCOM-add-member-button-container">
    <button class="CONCOM-add-member-button" @click="addStaffClicked" v-if="canAddDept">Add someone to {{department.name}}</button>
  </div>
`;

const isDepartment = (department) => Vue.computed(() => {
  return department.parentId !== undefined;
});

const divisionNavigationRef = (division) => Vue.computed(() => {
  return `#${division.name}`
});

const tableClass = (department) => Vue.computed(() => {
  return isDepartment(department).value ? 'CONCOM-list-department-container' : 'CONCOM-list-division-container';
});

const filterStaff = (staff, staffPositions, department) => {
  const divisionPositions = staffPositions.divisionPositions.map(position => position.name);

  if (!isDepartment(department).value) {
    return staff.filter((item) => divisionPositions.includes(item.position));
  }

  if (department.parentId === department.id) {
    return staff.filter((item) => !divisionPositions.includes(item.position));
  }

  return staff;
};

const INITIAL_DATA = () => {
  return {
    isDepartment,
    divisionNavigationRef,
    tableClass,
    filterStaff
  }
};

const canAddDepartmentStaff = (departmentId, permissions) => {
  const addAny = permissions.find((permission) => permission.subtype === 'api.post.staff.all')?.allowed === 1;
  const addDept = permissions.find((permission) => permission.subtype === `api.post.staff.${departmentId}`)?.allowed === 1;

  return addAny || addDept;
}

function addStaffClicked() {
  const isDepartment = this.isDepartment(this.department).value;
  this.updateSidebarProps(isDepartment);
}

function editStaffClicked(eventData) {
  this.updateSidebarProps(eventData.isDepartment, eventData.staff);
}

function updateSidebarProps(isDepartment, editedStaff) {
  this.showSidebar = !this.showSidebar;

  if (this.showSidebar) {
    this.sidebarDept = this.department;
    this.sidebarDivision = this.division;
    this.sidebarDeptStaff = this.departmentStaff;
    this.sidebarDeptIsDepartment = isDepartment;
    this.sidebarEditStaff = editedStaff ?? {};
  } else {
    this.sidebarDept = {};
    this.sidebarDivision = {};
    this.sidebarDeptStaff = [];
    this.sidebarDeptIsDepartment = false;
    this.sidebarEditStaff = {};
  }
}

function componentSetup(props) {
  const staffPositions = Vue.inject('staffPositions');
  const showSidebar = Vue.inject('showSidebar');
  const sidebarDept = Vue.inject('sidebarDept');
  const sidebarDivision = Vue.inject('sidebarDivision');
  const sidebarDeptStaff = Vue.inject('sidebarDeptStaff');
  const sidebarDeptIsDepartment = Vue.inject('sidebarDeptIsDepartment');
  const sidebarEditStaff = Vue.inject('sidebarEditStaff');
  const currentUser = Vue.inject('currentUser');

  const departmentStaff = Vue.ref([]);

  Vue.watchEffect(() => {
    if (props.divisionStaffMap != null && Array.isArray(props.divisionStaffMap[props.department.id])) {
      const staff = props.divisionStaffMap[props.department.id];
      const filteredStaff = filterStaff(staff, staffPositions.value, props.department);
      departmentStaff.value.push(...filteredStaff);
    }
  });

  return {
    departmentStaff,
    canAddDept: Vue.ref(false),
    staffPositions: staffPositions,
    showSidebar,
    sidebarDept,
    sidebarDivision,
    sidebarDeptStaff,
    sidebarDeptIsDepartment,
    sidebarEditStaff,
    currentUser
  }
}

const staffDepartmentComponent = {
  props: PROPS,
  template: TEMPLATE,
  data: INITIAL_DATA,
  setup: componentSetup,
  mounted() {
    const departmentId = this.department.id;
    const permissions = this.currentUser.permissions;
    this.canAddDept = canAddDepartmentStaff(departmentId, permissions);
  },
  methods: {
    addStaffClicked,
    editStaffClicked,
    updateSidebarProps
  }
};

export default staffDepartmentComponent;
