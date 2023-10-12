/* globals Vue, apiRequest */
const PROPS = {
  department: Object,
  division: Object,
  divisionStaffMap: Object
}

const TEMPLATE = `
  <div class="UI-table UI-table-heading" v-if="isDepartment(department).value">
    <div class="UI-table-row event-color-primary">
      <div class="UI-table-cell-no-border">{{department.name}}</div>
      <div class="UI-table-cell-no-border">
        <a :href="divisionNavigationRef(division).value">{{division.name}}</a>
      </div>
      <div class="UI-table-cell-no-border">
        <template v-for="email in department.email">{{email}}<br/></template>
      </div>
    </div>
  </div>
  <div :class="tableClass(department).value">
    <department-header :isDepartment=isDepartment(department).value :department=department></department-header>
    <div class="UI-table-row" v-for="staff in departmentStaff">
      <department-member :staff=staff :isDepartment=isDepartment(department).value :canEdit=canEditDept 
        @edit-clicked="editStaffClicked"></department-member>
    </div>
  </div>
  <div class="UI-center">
    <button class="UI-yellowbutton" @click="addStaffClicked">Add someone to {{department.name}}</button>
  </div>
`;

const isDepartment = (department) => Vue.computed(() => {
  return department.parentId !== undefined;
});

const divisionNavigationRef = (division) => Vue.computed(() => {
  return `#${division.name}`
});

const tableClass = (department) => Vue.computed(() => {
  return isDepartment(department) ? 'UI-table-all UI-table-heading' : 'UI-table-all';
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

const canEditDepartmentStaff = async(department, currentUser) => {
  // Bypass for users who have "edit any" permission, no need to check additional depts.
  if (currentUser.editAnyAllowed) {
    return {
      allowed: true
    }
  }

  const response = await apiRequest('GET', `permissions/generic/staff.${department.id}/put`);
  const canEditData = JSON.parse(response.responseText);

  return {
    allowed: canEditData.data[0].allowed
  };
}

const onMounted = async(componentInstance) => {
  const [ canEditResult ] = await Promise.all([
    canEditDepartmentStaff(componentInstance.department.id, componentInstance.currentUser)
  ]);

  componentInstance.canEditDept = canEditResult.allowed;
};

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
    canEditDept: Vue.ref(false),
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
  async mounted() {
    await onMounted(this);
  },
  methods: {
    addStaffClicked,
    editStaffClicked,
    updateSidebarProps
  }
};

export default staffDepartmentComponent;
