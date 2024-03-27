const PROPS = {
  name: String,
  data: Object
};

const TEMPLATE = `
  <template v-if="name != null">
    <div class="UI-sidebar-shown UI-fixed">
      <template v-if="name === 'department'">
        <staff-sidebar-department :data="data" @add-email-clicked="addEmailClicked"
          @edit-email-clicked="editEmailClicked"
          @sidebar-closed="$emit('sidebarClosed')" @save-department-clicked="saveDepartmentClicked"
          @delete-department-clicked="deleteDepartmentClicked" @add-permissions-clicked="addDepartmentPermissionsClicked"></staff-sidebar-department>
      </template>
      <template v-if="name === 'department_email'">
        <staff-sidebar-email :data="data" @close-clicked="closeEmailClicked" @save-email-clicked="saveEmailClicked"
          @delete-email-clicked="deleteEmailClicked"></staff-sidebar-email>
      </template>
      <template v-if="name === 'department_permissions'">
        <staff-sidebar-department-permissions :data="data" @close-clicked="closeDepartmentPermissionsClicked"
          @add-permission-clicked="addPermissionClicked" @delete-permission-clicked="deletePermissionClicked"></staff-sidebar-department-permissions>
      </template>
      <template v-if="name === 'permissions'">
        <staff-sidebar-permissions :data="data" @close-clicked="closeAddPermissionClicked"
          @save-permission-clicked="savePermissionClicked"></staff-sidebar-permissions>
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

function editEmailClicked(data, departmentEmail) {
  const eventData = {
    eventName: 'displayEmail',
    sidebarData: data,
    viewName: this.name,
    newView: 'department_email',
    newData: {
      email: departmentEmail
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

function deleteEmailClicked(data) {
  const eventData = {
    eventName:'deleteEmail',
    sidebarData: data
  };

  this.$emit('sidebarDeleteClicked', eventData);
}

function addDepartmentPermissionsClicked(data) {
  const eventData = {
    eventName: 'addDepartmentPermissions',
    viewName: this.name,
    sidebarData: data,
    newView: 'department_permissions',
    newData: {
      departmentId: data.id
    }
  }

  this.$emit('sidebarViewChanged', eventData);
}

function closeDepartmentPermissionsClicked() {
  const eventData = {
    eventName: 'closeDepartmentPermissions'
  }

  this.$emit('sidebarViewChanged', eventData);
}

function addPermissionClicked(data) {
  const eventData = {
    eventName: 'addPermission',
    viewName: this.name,
    sidebarData: {
      departmentId: data.departmentId
    },
    newView: 'permissions',
    newData: {
      departmentId: data.departmentId,
      positionId: data.positionId
    }
  }

  this.$emit('sidebarViewChanged', eventData);
}

function closeAddPermissionClicked() {
  const eventData = {
    eventName: 'closeAddPermission'
  }

  this.$emit('sidebarViewChanged', eventData);
}

function savePermissionClicked(data) {
  const eventData = {
    eventName: 'savePermission',
    sidebarData: data
  }

  this.$emit('sidebarSaveClicked', eventData);
}

function deletePermissionClicked(data) {
  const eventData = {
    eventName: 'deletePermission',
    sidebarData: data
  }

  this.$emit('sidebarDeleteClicked', eventData);
}

const StaffStructureSidebar = {
  props: PROPS,
  emits: ['sidebarClosed', 'sidebarViewChanged', 'sidebarSaveClicked', 'sidebarDeleteClicked'],
  methods: {
    addEmailClicked,
    closeEmailClicked,
    saveDepartmentClicked,
    deleteDepartmentClicked,
    saveEmailClicked,
    editEmailClicked,
    deleteEmailClicked,
    addDepartmentPermissionsClicked,
    closeDepartmentPermissionsClicked,
    addPermissionClicked,
    closeAddPermissionClicked,
    savePermissionClicked,
    deletePermissionClicked
  },
  template: TEMPLATE
};

export default StaffStructureSidebar;
