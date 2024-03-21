/* globals Vue, showSpinner, hideSpinner */
import { useCurrentUser } from '../composables/currentUser.js';
import { useDivisionHierarchy } from '../composables/divisionHierarchy.js';
import { useSidebar } from '../composables/sidebar.js';
import { useSidebarActions } from '../composables/sidebarActions.js';

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
              <button class="UI-redbutton UI-padding UI-margin" @click="addDepartmentPermissionsClicked">All ConCom Site Permissions (RBAC)</button>
            </div>
          </template>
          
          <staff-structure-division :divisions="divisions" @add-division="addDepartmentClicked" 
            @add-department="addDepartmentClicked" @edit-department="editDepartmentClicked" 
            @edit-division="editDepartmentClicked"></staff-structure-division>
        </div>
      </div>
    </div>

    <staff-structure-sidebar :name="sidebarName" :data="sidebarData" 
      @sidebar-view-changed="sidebarViewChanged" @sidebar-closed="sidebarClosed"
      @sidebar-save-clicked="sidebarSaveClicked" @sidebar-delete-clicked="sidebarDeleteClicked"></staff-structure-sidebar>
  </div>
`;

function setup() {
  const { user, loadingUser, fetchUser, hasPermission } = useCurrentUser();
  const { divisions, loadingDivisions, fetchDivisions } = useDivisionHierarchy();
  const { showSidebar, sidebarName, sidebarData, prepareSidebar, changeSidebar, closeSidebar } = useSidebar();
  const { saveSidebarData, deleteSidebarData } = useSidebarActions();
  const canEditRbac = Vue.ref(false);

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
    prepareSidebar,
    changeSidebar,
    closeSidebar,
    saveSidebarData,
    deleteSidebarData,
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
  this.prepareSidebar({
    eventName: 'addDepartment',
    division
  });
}

function editDepartmentClicked(existingData) {
  this.prepareSidebar({
    eventName: 'editDepartment',
    existingData
  });
}

function sidebarViewChanged(data) {
  this.changeSidebar(data);
}

function sidebarClosed() {
  this.closeSidebar();
}

async function sidebarSaveClicked(data) {
  try {
    await this.saveSidebarData(data);

    if (data.eventName === 'saveEmail') {
      this.sidebarViewChanged({ eventName: 'closeEmail' });
    } else {
      await this.fetchDivisions();
      this.closeSidebar();
    }
  } catch (error) {
    console.error(error);
  }
}

async function sidebarDeleteClicked(data) {
  try {
    await this.deleteSidebarData(data);

    if (data.eventName === 'deleteEmail') {
      this.sidebarViewChanged({ eventName: 'closeEmail' });
    } else {
      await this.fetchDivisions();
      this.closeSidebar();
    }
  } catch (error) {
    console.error(error);
  }
}

function addDepartmentPermissionsClicked() {
  this.prepareSidebar({
    eventName: 'addDepartmentPermissions',
    departmentId: 'all'
  });
}

const StaffStructurePage = {
  setup,
  created: onCreated,
  methods: {
    addDepartmentClicked,
    editDepartmentClicked,
    sidebarClosed,
    sidebarViewChanged,
    sidebarSaveClicked,
    sidebarDeleteClicked,
    addDepartmentPermissionsClicked
  },
  template: TEMPLATE
};

export default StaffStructurePage;
