/* globals apiRequest, Vue */
import { extractDivisionHierarchy } from '../division-parser.js';

const SUBHEAD_POSITION_ID = 2;

const TEMPLATE = `
  <div :class="contentAreaClass(showSidebar).value">
    <div class="UI-event-sectionbar">ConCom</div>
    <div class="UI-maincontent">
      <staff-division v-for="division in divisions" :division=division></staff-division>
    </div>
  </div>
  <staff-sidebar v-if="showSidebar" :department=sidebarDept :isDepartment=sidebarDeptIsDepartment 
    @sidebar-closed="closeSidebar" @sidebar-form-submitted="updateDepartment"></staff-sidebar>
`;

const fetchDivisionData = async() => {
  const response = await apiRequest('GET', 'department');
  const departmentData = JSON.parse(response.responseText);

  return extractDivisionHierarchy(departmentData.data);
};

const fetchCurrentUser = async() => {
  const response = await apiRequest('GET', 'member');
  const memberData = JSON.parse(response.responseText);

  return {
    id: parseInt(memberData.id)
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
  // Make sure we have all of the data we need before allowing child component rendering.
  const [divisionResult, userResult, positionResult] = await Promise.all([
    fetchDivisionData(),
    fetchCurrentUser(),
    fetchStaffPositions()
  ]);

  componentInstance.divisions.push(...divisionResult);
  componentInstance.currentUser = userResult;
  componentInstance.staffPositions = positionResult;
};

function componentSetup() {
  const currentUser = Vue.ref({ id: null });
  Vue.provide('currentUser', Vue.readonly(currentUser));

  const divisions = Vue.ref([]);
  Vue.provide('divisions', Vue.readonly(divisions));

  const staffPositions = Vue.ref({ departmentPositions: [], divisionPositions: [] });
  Vue.provide('staffPositions', Vue.readonly(staffPositions));

  const showSidebar = Vue.ref(false);
  Vue.provide('showSidebar', showSidebar);

  const sidebarDept = Vue.ref({});
  Vue.provide('sidebarDept', sidebarDept);

  const sidebarDeptStaff = Vue.ref([]);
  Vue.provide('sidebarDeptStaff', sidebarDeptStaff);

  const sidebarDeptIsDepartment = Vue.ref(false);
  Vue.provide('sidebarDeptIsDepartment', sidebarDeptIsDepartment);
  return {
    currentUser,
    divisions,
    staffPositions,
    showSidebar,
    sidebarDept,
    sidebarDeptStaff,
    sidebarDeptIsDepartment
  }
}

const contentAreaClass = (showSidebar) => Vue.computed(() => {
  return showSidebar ? 'UI-maincontent UI-mainsection-sidebar-shown' : 'UI-maincontent UI-rest';
});

function closeSidebar() {
  this.showSidebar = false;
}

function updateDepartment(eventData) {
  if (eventData.eventType === 'added') {
    this.sidebarDeptStaff.push(eventData.staff);
  }
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
