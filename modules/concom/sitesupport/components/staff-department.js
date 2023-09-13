/* globals Vue, apiRequest */
import { extractDepartmentStaff } from '../department-staff-parser.js';

const PROPS = {
  department: Object,
  division: Object
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
      <department-member :staff=staff :isDepartment=isDepartment(department).value></department-member>
    </div>
  </div>
  <div class="UI-center">
    <button class="UI-yellowbutton" @click="addStaffClicked(department, isDepartment(department).value)">Add someone to {{department.name}}</button>
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

const filterStaff = (componentInstance, staff) => {
  const divisionPositions = componentInstance.staffPositions.divisionPositions.map(position => position.name);

  if (!isDepartment(componentInstance.department).value) {
    return staff.filter((item) => divisionPositions.includes(item.position));
  }

  if (componentInstance.department.parentId === componentInstance.department.id) {
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

const fetchDepartmentStaff = async(departmentId) => {
  const response = await apiRequest('GET', `department/${departmentId}/staff`);
  const departmentStaffData = JSON.parse(response.responseText);

  return extractDepartmentStaff(departmentStaffData.data);
};

const onMounted = async(componentInstance) => {
  const departmentStaff = await fetchDepartmentStaff(componentInstance.department.id);
  const filteredStaff = filterStaff(componentInstance, departmentStaff);
  componentInstance.departmentStaff.push(...filteredStaff);
};

function addStaffClicked(department, isDepartment) {
  this.showSidebar = !this.showSidebar;
  this.sidebarDept = this.showSidebar ? department : {};
  this.sidebarDeptStaff = this.showSidebar ? this.departmentStaff : [];
  this.sidebarDeptIsDepartment = this.showSidebar ? isDepartment : false;
}

function componentSetup() {
  const staffPositions = Vue.inject('staffPositions');
  const showSidebar = Vue.inject('showSidebar');
  const sidebarDept = Vue.inject('sidebarDept');
  const sidebarDeptStaff = Vue.inject('sidebarDeptStaff');
  const sidebarDeptIsDepartment = Vue.inject('sidebarDeptIsDepartment');

  return {
    departmentStaff: Vue.ref([]),
    staffPositions: staffPositions,
    showSidebar,
    sidebarDept,
    sidebarDeptStaff,
    sidebarDeptIsDepartment
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
    addStaffClicked
  }
};

export default staffDepartmentComponent;
