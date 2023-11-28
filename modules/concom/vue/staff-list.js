/* globals apiRequest, Vue */
import { extractDivisionHierarchy } from '../sitesupport/division-parser.js';
import { extractDepartmentStaff } from '../sitesupport/department-staff-parser.js';

const SUBHEAD_POSITION_ID = 2;

const TEMPLATE = `
  <div :class="contentAreaClass(showSidebar).value">
    <div class="CONCOM-list-page-top-section">ConCom</div>
    <org-donut :divisions=divisions></org-donut>
    <div class="CONCOM-list-page-content">
      <staff-division v-for="division in divisions" :division=division :divisionStaff=divisionStaffMap[division.id]></staff-division>
    </div>
  </div>
  <staff-sidebar v-if="showSidebar" @sidebar-closed="closeSidebar" @sidebar-form-submitted="updateDepartment"></staff-sidebar>
`;

const fetchDivisionData = async() => {
  const response = await apiRequest('GET', 'department');
  const departmentData = JSON.parse(response.responseText);

  return extractDivisionHierarchy(departmentData.data);
};

const fetchCurrentUser = async() => {
  const response = await apiRequest('GET', 'member');
  const memberData = JSON.parse(response.responseText);

  const userPermissions = await apiRequest('GET', `member/${memberData.id}/permissions?max_results=all`);
  const permissionData = JSON.parse(userPermissions.responseText);

  return {
    id: parseInt(memberData.id),
    permissions: permissionData.data
  };
};

const fetchStaffPositions = async() => {
  const response = await apiRequest('GET', 'staff/positions');
  const positionData = JSON.parse(response.responseText);

  const positionMapping = positionData.data.reduce((prev, current) => {
    const mappedPosition = {
      id: parseInt(current.id),
      name: current.position
    };

    if (mappedPosition.id !== SUBHEAD_POSITION_ID) {
      prev.divisionPositions.push(mappedPosition);
    }

    prev.departmentPositions.push(mappedPosition);
    return prev;
  }, {
    departmentPositions: [],
    divisionPositions: []
  });

  return positionMapping;
}

const onMounted = async(componentInstance) => {
  componentInstance.updateLoading(true);
  // Make sure we have all of the data we need before allowing child component rendering.
  const [divisionResult, userResult, positionResult] = await Promise.all([
    fetchDivisionData(),
    fetchCurrentUser(),
    fetchStaffPositions()
  ]);

  for await (const division of divisionResult) {
    const divisionStaffResponse = await apiRequest('GET', `department/${division.id}/staff?subdepartments=1&max_results=all`);
    const divisionStaffData = JSON.parse(divisionStaffResponse.responseText);
    const parsed = extractDepartmentStaff(divisionStaffData.data);

    componentInstance.divisionStaffMap[division.id] = parsed;
  }

  componentInstance.divisions.push(...divisionResult);
  componentInstance.currentUser = userResult;
  componentInstance.staffPositions = positionResult;
  componentInstance.updateLoading(false);
};

function componentSetup() {
  const currentUser = Vue.ref({ id: null, permissions: [] });
  Vue.provide('currentUser', Vue.readonly(currentUser));

  const divisions = Vue.ref([]);
  Vue.provide('divisions', Vue.readonly(divisions));

  const staffPositions = Vue.ref({ departmentPositions: [], divisionPositions: [] });
  Vue.provide('staffPositions', Vue.readonly(staffPositions));

  const divisionStaffMap = Vue.ref({});

  const showSidebar = Vue.ref(false);
  Vue.provide('showSidebar', showSidebar);

  const sidebarDept = Vue.ref({});
  Vue.provide('sidebarDept', sidebarDept);

  const sidebarDivision = Vue.ref({});
  Vue.provide('sidebarDivision', sidebarDivision);

  const sidebarDeptStaff = Vue.ref([]);
  Vue.provide('sidebarDeptStaff', sidebarDeptStaff);

  const sidebarDeptIsDepartment = Vue.ref(false);
  Vue.provide('sidebarDeptIsDepartment', sidebarDeptIsDepartment);

  const sidebarEditStaff = Vue.ref({});
  Vue.provide('sidebarEditStaff', sidebarEditStaff);

  // eslint-disable-next-line no-unused-vars
  const { _, updateLoading } = Vue.inject('isLoading');
  return {
    currentUser,
    divisions,
    staffPositions,
    divisionStaffMap,
    showSidebar,
    sidebarDept,
    sidebarDivision,
    sidebarDeptStaff,
    sidebarDeptIsDepartment,
    sidebarEditStaff,
    updateLoading
  }
}

const contentAreaClass = (showSidebar) => Vue.computed(() => {
  return showSidebar ? 'CONCOM-list-page-sidebar-active' : 'CONCOM-list-page';
});

function closeSidebar() {
  this.showSidebar = false;
  this.sidebarDept = {};
  this.sidebarDeptStaff = [];
  this.sidebarDeptIsDepartment = false;
  this.sidebarEditStaff = {};
}

function updateDepartment(eventData) {
  if (eventData.eventType === 'added') {
    this.sidebarDeptStaff.push(eventData.staff);
  } else if (eventData.eventType === 'updated') {
    const updatedStaff = this.sidebarDeptStaff.find((staff) => staff.id === eventData.staff.id);
    if (updatedStaff != null) {
      updatedStaff.note = eventData.staff.note;
      updatedStaff.position = eventData.staff.position;
    }
  } else if (eventData.eventType === 'removed') {
    const staffIdx = this.sidebarDeptStaff.findIndex((staff) => staff.id === eventData.staff.id);
    if (staffIdx !== -1) {
      this.sidebarDeptStaff.splice(staffIdx, 1);
    }
  }

  this.closeSidebar();
}

const staffListComponent = {
  template: TEMPLATE,
  setup: componentSetup,
  async mounted() {
    await onMounted(this);
  },
  methods: {
    contentAreaClass,
    closeSidebar,
    updateDepartment
  }
};

export default staffListComponent;
