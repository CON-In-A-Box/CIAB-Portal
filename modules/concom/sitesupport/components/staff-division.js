/* globals Vue, apiRequest */
import { extractDepartmentStaff } from '../department-staff-parser.js';

const PROPS = {
  division: Object,
  divisionHierarchy: Array,
  currentUser: Object
};

const TEMPLATE = `
  <div class="UI-table UI-table-heading" :id="htmlTagFriendlyName(division).value">
    <div class="UI-table-row event-color-secondary">
      <div class="UI-center UI-table-cell-no-border">{{divisionName(division).value}}</div>
      <div class="UI-center UI-table-cell-no-border">
        <staff-section-nav :divisionContent=divisionHierarchy :id="htmlTagFriendlyName(division).value + '_nav'"></staff-section-nav>
      </div>
      <div class="UI-table-cell-no-border">
        <p v-for="email in division.email">{{email}}</p>
      </div> 
    </div>
  </div>
  <div class="UI-table-all">
    <department-header :isDepartment=false :departmentName=division.name></department-header>
    <div class="UI-table-row" v-for="staff in divisionStaff">
      <department-member :staff=staff :currentUser=currentUser></department-member>
    </div>
  </div>
`;

const htmlTagFriendlyName = (division) => Vue.computed(() => {
  return division?.name.replace(' ', '_');
});

const divisionName = (division) => Vue.computed(() => {
  return division.specialDivision ? division.name : `${division.name} Division`;
});

const INITIAL_DATA = () => {
  return {
    htmlTagFriendlyName,
    divisionName,
    divisionStaff: []
  }
};

const fetchDepartmentStaff = async(divisionId) => {
  const response = await apiRequest('GET', `department/${divisionId}/staff`);
  const departmentStaffData = JSON.parse(response.responseText);

  return extractDepartmentStaff(departmentStaffData.data);
};

const onMounted = async(componentInstance) => {
  const departmentStaff = await fetchDepartmentStaff(componentInstance.division.id);
  const divisionStaff = departmentStaff.filter((item) => item.position === 'Director' || item.position === 'Support');
  componentInstance.divisionStaff.push(...divisionStaff);
}

const staffDivisionComponent = {
  props: PROPS,
  template: TEMPLATE,
  data: INITIAL_DATA,
  async mounted() {
    await onMounted(this);
  }
};

export default staffDivisionComponent;
