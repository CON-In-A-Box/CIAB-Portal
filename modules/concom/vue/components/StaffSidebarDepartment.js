/* globals Vue, showSpinner, hideSpinner */
import { useDepartmentStaff } from '../composables/departmentStaff.js';

const PROPS = {
  data: Object
};

const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Position Details</h2>
  </div>
  <div>
    <hr/>
    <label class="UI-label" for="position_name">Position Name:</label>
    <input class="UI-input" id="position_name" v-model="departmentName" />

    <label class="UI-label" for="department_email">Position Emails:</label>
    <div class="UI-border" id="department_email">
      <template v-for="email in departmentEmails">
        <button class="UI-roundbutton">{{ email }}</button>
        <br/>
      </template>
      <button class="UI-roundbutton" @click="addEmailClicked"><i class="fas fa-plus-square"></i></button>
    </div>

    <button class="UI-redbutton UI-padding UI-margin" v-if="canEditRbac" disabled="departmentId === -1">
      Position Site Permissions (RBAC)
    </button><br/>

    <label class="UI-label" for="department_staff_count">Staff Count:</label>
    <input class="UI-input" id="department_staff_count" readonly :value="staffCount" />

    <div v-if="isDivisionToggle">
      <label class="UI-label" for="department_subdepartments">Sub Departments:</label>
      <input class="UI-input" id="department_subdepartments" readonly :value="subDepartments" />
    </div>

    <div v-if="isDivisionToggle">
      <label class="UI-label" for="department_fallback_dept">Fallback For:</label>
      <select class="UI-select" style="width:auto" id="department_fallback_dept" v-model="selectedFallbackDepartment">
        <option disabled value="">Please select a fallback department</option>
        <option v-for="division in fallbackDivisions" :key="division.id" :value="division">
          {{ division.name }}
        </option>
      </select>
    </div>
    <div v-else>
      <label class="UI-label" for="parent_department">Division:</label>
      <select class="UI-select" style="width:auto" id="parent_department" v-model="selectedParentDepartment">
        <option disabled value="">Please select a parent department</option>
        <option v-for="division in divisions" :key="division.id" :value="division">
          {{ division.name }}
        </option>
      </select>
    </div>
  </div>
  <div>
    <div class="UI-table switch-table UI-padding UI-center">
      <div class="UI-table-row">
        <div class="UI-table-cell">
          <span class="UI-padding">Department</span>
          <label class="switch">
            <input class="toggle" type="checkbox" v-model="isDivisionToggle"/>
            <div class="slider"></div>
          </label>
          <span class="UI-padding">Division</span>
        </div>
      </div>
    </div>
    <div class="UI-center">
      <hr/>
      <button class="UI-eventbutton" @click="onSave">Save</button>
      <button class="UI-yellowbutton" @click="$emit('sidebarClosed')">Close</button>
      <button class="UI-redbutton" @click="onDelete" :disabled="departmentId === -1 || subDepartments > 0 || staffCount > 0">Delete</button>
    </div>
  </div>
`;

function addEmailClicked() {
  const data = {
    id: this.departmentId,
    name: this.departmentName,
    email: this.departmentEmails,
    permissions: this.departmentPermissions,
    staffCount: this.staffCount,
    departments: this.subDepartments,
    fallback: this.selectedFallbackDepartment,
    parentId: this.selectedParentDepartment,
    isDivision: this.isDivisionToggle
  };

  this.$emit('addEmailClicked', data);
}

function onSave() {
  const data = {
    id: this.departmentId,
    departmentName: this.departmentName,
    fallbackDepartment: this.selectedFallbackDepartment,
    parentDepartment: this.selectedParentDepartment,
    isDivision: this.isDivisionToggle
  };

  this.$emit('saveDepartmentClicked', data);
}

function onDelete() {
  const data = {
    id: this.departmentId
  };

  this.$emit('deleteDepartmentClicked', data);
}

function setup(props) {
  const departmentId = Vue.ref(props.data?.id ?? -1);
  const departmentName = Vue.ref(props.data?.name ?? (props.data?.isDivision ? 'New Division' : 'New Department'));
  const departmentEmails = Vue.ref(props.data?.email ?? []);
  const departmentPermissions = Vue.ref(props.data?.permissions ?? { 'Head': [], 'Sub-Head': [], 'Specialist': [] });
  const staffCount = Vue.ref(props.data?.staffCount ?? 0);
  const subDepartments = Vue.ref(props.data?.subDepartments ?? 0);
  const isDivisionToggle = Vue.ref(props.data?.isDivision ?? props.isDivision);

  const { departmentStaff, loadingDepartment, departmentError, fetchDepartmentStaff } = useDepartmentStaff();

  const canEditRbac = Vue.inject('canEditRbac');
  const divisions = Vue.inject('divisions');

  const fallbackDepartment = props.data?.fallback ? divisions.value.find((division) => division.id === props.data?.fallback) : {};
  const parentDepartment = props.data?.parentId ? divisions.value.find((division) => division.id === props.data?.parentId) : {};

  const selectedFallbackDepartment = Vue.ref(fallbackDepartment);
  const selectedParentDepartment = Vue.ref(parentDepartment);

  const fallbackReferences = divisions.value.filter((division) => division.fallback != null).map((division) => division.fallback);
  const fallbackDivisions = divisions.value.filter((division) => division.id !== departmentId &&
    (fallbackDepartment?.id === division.id || !fallbackReferences.includes(division.fallback)));

  Vue.watch(departmentStaff, () => {
    staffCount.value = departmentStaff.value?.length ?? 0;
  })

  Vue.watch(loadingDepartment, () => {
    if (loadingDepartment.value) {
      showSpinner();
    } else {
      hideSpinner();
    }
  });

  return {
    departmentId,
    departmentName,
    departmentEmails,
    departmentPermissions,
    staffCount,
    subDepartments,
    selectedFallbackDepartment,
    selectedParentDepartment,
    isDivisionToggle,
    canEditRbac: canEditRbac.value,
    divisions,
    fallbackDivisions,
    departmentStaff,
    loadingDepartment,
    departmentError,
    fetchDepartmentStaff
  }
}

async function onCreated() {
  await this.fetchDepartmentStaff(this.departmentId);
}

const StaffSidebarDepartment = {
  props: PROPS,
  emits: ['sidebarClosed', 'addEmailClicked', 'saveDepartmentClicked', 'deleteDepartmentClicked'],
  setup,
  created: onCreated,
  methods: {
    addEmailClicked,
    onSave,
    onDelete
  },
  template: TEMPLATE
};

export default StaffSidebarDepartment;
