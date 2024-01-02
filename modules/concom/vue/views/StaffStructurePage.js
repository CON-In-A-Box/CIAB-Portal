/* globals Vue, showSpinner, hideSpinner */
import { useCurrentUser } from '../composables/currentUser.js';
import { useDivisionHierarchy } from '../composables/divisionHierarchy.js';

const TEMPLATE = `
  <div class="UI-container">
    <div :class="sidebarDisplayClass">
      <div class="CONCOM-admin-content">
        <div class="CONCOM-admin-section">
          <span>ConCom Structure</span>
        </div>
        
        <div class="UI-maincontent">
          <div class="UI-container UI-padding UI-center">
            Basic Usage:
            <p>Use the <i class="fas fa-plus-square"></i> to add a new department</p>
            <p>Use the <span class="UI-yellow">Add New Division<i class="fas fa-plus-square"></i></span> button to add a new Division</p>
            <p>Drag departments around to change the division</p>
            <p>Double click on Divisions or Departments to change the properties</p>
          </div>

          <template v-if="canEditRbac">
            <div class="UI-container UI-margin UI-center">
              <button class="UI-redbutton UI-padding UI-margin">All ConCom Site Permissions (RBAC)</button>
            </div>
          </template>
          
          <staff-structure-division :divisions="divisions" @add-division="addDepartmentClicked" 
            @add-department="addDepartmentClicked"></staff-structure-division>
        </div>
      </div>
    </div>

    <staff-structure-sidebar :name="sidebarName" :data="sidebarData" 
      @sidebar-closed="closeSidebarClicked"></staff-structure-sidebar>
  </div>
`;

function setup() {
  const { user, loadingUser, fetchUser, hasPermission } = useCurrentUser();
  const { divisions, loadingDivisions, fetchDivisions } = useDivisionHierarchy();
  const canEditRbac = Vue.ref(false);
  const showSidebar = Vue.ref(false);
  const sidebarName = Vue.ref(null);
  const sidebarData = Vue.ref(null);

  const loadingValues = [loadingUser, loadingDivisions];
  const sidebarDisplayClass = Vue.computed(() => showSidebar.value ? 'UI-maincontent UI-mainsection-sidebar-shown' : 'UI-maincontent')

  Vue.watch([loadingUser, loadingDivisions], () => {
    loadingValues.some((item) => item.value) ? showSpinner() : hideSpinner();
  });

  Vue.watch(user, () => {
    canEditRbac.value = hasPermission('site.concom.permissions');
  });

  Vue.provide('canEditRbac', canEditRbac);
  Vue.provide('divisions', divisions);

  return {
    fetchUser,
    fetchDivisions,
    divisions,
    canEditRbac,
    showSidebar,
    sidebarName,
    sidebarData,
    sidebarDisplayClass
  }
}

async function onCreated() {
  await Promise.all([
    this.fetchUser(),
    this.fetchDivisions()
  ]);
}

function addDepartmentClicked(division) {
  if (division == null) {
    this.sidebarData = { isDivision: true };
  } else {
    this.sidebarData = { isDivision: false };
  }

  this.sidebarName = 'department';
  this.showSidebar = !this.showSidebar;
}

function closeSidebarClicked() {
  this.showSidebar = false;
  this.sidebarName = null;
}

const StaffStructurePage = {
  setup,
  created: onCreated,
  methods: {
    addDepartmentClicked,
    closeSidebarClicked
  },
  template: TEMPLATE
};

export default StaffStructurePage;
