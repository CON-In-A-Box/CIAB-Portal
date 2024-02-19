const PROPS = {
  name: String,
  data: Object
};

const TEMPLATE = `
  <template v-if="name != null">
    <div class="UI-sidebar-shown UI-fixed">
      <template v-if="name === 'department'">
        <staff-sidebar-department :data="data" @add-email-clicked="addEmailClicked" 
          @sidebar-closed="$emit('sidebarClosed')" @save-department-clicked="saveDepartmentClicked"
          @delete-department-clicked="deleteDepartmentClicked"></staff-sidebar-department>
      </template>
      <template v-if="name === 'department_email'">
        <staff-sidebar-email :data="data" @close-clicked="closeEmailClicked" @save-email-clicked="saveEmailClicked"></staff-sidebar-email>
      </template>
      <template v-if="name === 'department_permissions'">
        <staff-sidebar-department-permissions></staff-sidebar-department-permissions>
      </template>
      <template v-if="name === 'permissions'">
        <staff-sidebar-permissions></staff-sidebar-permissions>
      </template>
    </div>
  </template>
  <template v-else>

  </template>
`;

function addEmailClicked(data) {
  const eventData = {
    eventName: 'displayEmail',
    sidebarData: data,
    viewName: this.name,
    newView: 'department_email',
    newData: {
      departmentId: data.id
    }
  }

  this.$emit('sidebarViewChanged', eventData);
}

function closeEmailClicked() {
  const eventData = {
    eventName: 'closeEmail'
  }

  this.$emit('sidebarViewChanged', eventData);
}

function saveDepartmentClicked(data) {
  const eventData = {
    eventName: 'saveDepartment',
    sidebarData: data
  };

  this.$emit('sidebarSaveClicked', eventData);
}

function deleteDepartmentClicked(data) {
  const eventData = {
    eventName: 'deleteDepartment',
    sidebarData: data
  };

  this.$emit('sidebarDeleteClicked', eventData);
}

function saveEmailClicked(data) {
  const eventData = {
    eventName: 'saveEmail',
    sidebarData: data
  };

  this.$emit('sidebarSaveClicked', eventData);
}

const StaffStructureSidebar = {
  props: PROPS,
  emits: ['sidebarClosed', 'sidebarViewChanged', 'sidebarSaveClicked', 'sidebarDeleteClicked'],
  methods: {
    addEmailClicked,
    closeEmailClicked,
    saveDepartmentClicked,
    deleteDepartmentClicked,
    saveEmailClicked
  },
  template: TEMPLATE
};

export default StaffStructureSidebar;
