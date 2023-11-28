/* globals Vue */
import { sortStaffByPosition } from '../sitesupport/department-staff-parser.js';

const PROPS = {
  division: Object,
  divisionStaff: Array
};

const TEMPLATE = `
  <div class="CONCOM-list-division">
    <div class="CONCOM-list-division-separator" :id="htmlTagFriendlyName(division).value">
      <div class="CONCOM-list-division-separator-row">
        <div class="CONCOM-list-separator-row-column">{{divisionName(division).value}}</div>
        <div class="CONCOM-list-separator-row-column">
          <staff-section-nav :id="htmlTagFriendlyName(division).value + '_nav'"></staff-section-nav>
        </div>
        <div class="CONCOM-list-separator-row-column">
          <template v-for="email in division.email">{{email}}<br/></template>
        </div> 
      </div>
    </div>
    <staff-department :department=division :division=division :divisionStaffMap=divisionStaffMap></staff-department>
    <div class="CONCOM-list-department">
      <template v-for="department in division.departments">
        <staff-department :department=department :division=division :divisionStaffMap=divisionStaffMap></staff-department>
      </template>
    </div>
  </div>
`;

const htmlTagFriendlyName = (division) => Vue.computed(() => {
  return division.name.replaceAll(' ', '_');
});

const divisionName = (division) => Vue.computed(() => {
  return division.specialDivision ? division.name : `${division.name} Division`;
});

const INITIAL_DATA = () => {
  return {
    divisionStaffMap: {},
  }
};

const onMounted = async(componentInstance) => {
  for (const staff of componentInstance.divisionStaff) {
    const deptId = `${staff.deptId}`;
    if (componentInstance.divisionStaffMap[deptId] == null) {
      componentInstance.divisionStaffMap[deptId] = [];
    }

    componentInstance.divisionStaffMap[deptId].push(staff);
  }

  for (const key in componentInstance.divisionStaffMap) {
    componentInstance.divisionStaffMap[key].sort(sortStaffByPosition);
  }
}

const staffDivisionComponent = {
  props: PROPS,
  template: TEMPLATE,
  data: INITIAL_DATA,
  async mounted() {
    await onMounted(this);
  },
  methods: {
    htmlTagFriendlyName,
    divisionName
  }
};

export default staffDivisionComponent;
