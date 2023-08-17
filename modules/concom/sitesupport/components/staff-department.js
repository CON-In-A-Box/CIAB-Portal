/* globals Vue, apiRequest */
import { extractDepartmentStaff } from '../department-staff-parser.js';

const PROPS = {
  department: Object,
  division: Object,
  currentUser: Object
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
      <department-member :staff=staff :isDepartment=isDepartment(department).value :currentUser=currentUser></department-member>
    </div>
  </div>
`;

const DIVISION_POSITIONS = ['Head', 'Specialist'];

const isDepartment = (department) => Vue.computed(() => {
  return department.parentId !== undefined;
});

const divisionNavigationRef = (division) => Vue.computed(() => {
  return `#${division.name}`
});

const tableClass = (department) => Vue.computed(() => {
  return isDepartment(department) ? 'UI-table-all UI-table-heading' : 'UI-table-all';
});

const filterStaff = (department, staff) => {
  if (!isDepartment(department).value) {
    return staff.filter((item) => DIVISION_POSITIONS.includes(item.position));
  }

  if (department.parentId === department.id) {
    return staff.filter((item) => !DIVISION_POSITIONS.includes(item.position));
  }

  return staff;
};

const INITIAL_DATA = () => {
  return {
    isDepartment,
    divisionNavigationRef,
    tableClass,
    filterStaff,
    departmentStaff: []
  }
};

const fetchDepartmentStaff = async(departmentId) => {
  const response = await apiRequest('GET', `department/${departmentId}/staff`);
  const departmentStaffData = JSON.parse(response.responseText);

  return extractDepartmentStaff(departmentStaffData.data);
};

const onMounted = async(componentInstance) => {
  const departmentStaff = await fetchDepartmentStaff(componentInstance.department.id);
  const filteredStaff = filterStaff(componentInstance.department, departmentStaff);
  componentInstance.departmentStaff.push(...filteredStaff);
}

const staffDepartmentComponent = {
  props: PROPS,
  template: TEMPLATE,
  data: INITIAL_DATA,
  async mounted() {
    await onMounted(this);
  }
};

export default staffDepartmentComponent;
