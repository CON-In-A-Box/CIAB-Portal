const PROPS = {
  name: String,
  data: Object
};

const TEMPLATE = `
  <template v-if="name != null">
    <div class="UI-sidebar-shown UI-fixed">
      <template v-if="name === 'department'">
        <staff-sidebar-department :data="data" @sidebar-closed="$emit('sidebarClosed')"></staff-sidebar-department>
      </template>
      <template v-if="name === 'department_email'">
        <staff-sidebar-email></staff-sidebar-email>
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

const StaffStructureSidebar = {
  props: PROPS,
  emits: [ 'sidebarClosed' ],
  template: TEMPLATE
};

export default StaffStructureSidebar;
