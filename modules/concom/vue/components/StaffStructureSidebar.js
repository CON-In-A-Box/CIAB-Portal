const PROPS = {
  name: String
};

const TEMPLATE = `
  <div class="UI-sidebar-hidden UI-fixed" :id="name">
    <template v-if="name === 'edit_department_position'">
      <staff-sidebar-department :edit="true"></staff-sidebar-department>
    </template>
    <template v-if="name === 'edit_department_email'">
      <staff-sidebar-email :edit="true"></staff-sidebar-email>
    </template>
    <template v-if="name === 'edit_department_permissions'">
      <staff-sidebar-department-permissions :edit="true"></staff-sidebar-department-permissions>
    </template>
    <template v-if="name === 'add_permissions'">
      <staff-sidebar-permissions></staff-sidebar-permissions>
    </template>
  </div>
`;

const StaffStructureSidebar = {
  props: PROPS,
  template: TEMPLATE
};

export default StaffStructureSidebar;
