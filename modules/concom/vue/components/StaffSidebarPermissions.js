/* globals Vue */
const PROPS = {
  data: Object
};

// These really ought to live in the database.
const PERMISSIONS = [
  { name: 'admin.sudo', description: 'Allowed to change login id to any other users login.' },
  { name: 'api.get.deadline.all', description: 'Able to get deadlines from all departments.' },
  { name: 'api.delete.deadline.all', description: 'Able to delete deadlines from all departments.' },
  { name: 'api.post.deadline.all', description: 'Able to modify deadlines from all departments.' },
  { name: 'api.put.deadline.all', description: 'Able to add deadlines to all departments.' },
  { name: 'asset.admin', description: 'Able to upload and change site graphical assets' },
  { name: 'concom.reports', description: 'Generate a CSV report of the ConCom Membership' },
  { name: 'concom.view', description: 'View the ConCom list' },
  { name: 'concom.modify.all', description: 'Remove/modify the concom membership in any department.' },
  { name: 'concom.add.all', description: 'Add member to the concom of any department.' },
  { name: 'registration.reports', description: 'Allowed generate reports from convention registration data.' },
  { name: 'site.admin', description: 'Access to the main site administrator page(Superuser)' },
  { name: 'site.concom.permissions', description: 'Allowed to change role permissions' },
  { name: 'site.concom.structure', description: 'Allowed to change concom structure' },
  { name: 'site.email_lists', description: 'Allowed to change all email lists on system' },
  { name: 'volunteers.admin', description: 'Allowed administrate volunteer data.' },
  { name: 'volunteers.reports', description: 'Allowed generate reports from convention volunteer data.' }
];

const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Add Permission</h2>
  </div>
  <div>
    <hr/>
    <select class="UI-select" style="width:auto" id="updated_perm_perm" v-model="selectedPermission">
      <option disabled value="">Please select a permission</option>
      <option v-for="permission in permissionOptions" :key="permission.name" :value="permission">
        {{ permission.name }}
      </option>
    </select>
  </div>
  <div class="UI-center">
    <hr/>
    <button class="UI-eventbutton" :disabled="selectedPermission?.name == null">Save</button>
    <button class="UI-yellowbutton" @click="$emit('closeClicked')">Close</button>
  </div>
`;

function setup(props) {
  const selectedPermission = Vue.ref({});
  const permissionOptions = PERMISSIONS;
  const departmentId = props.data.departmentId;
  const positionId = props.data.positionId;

  return {
    selectedPermission,
    permissionOptions,
    departmentId,
    positionId
  }
}

const StaffSidebarPermissions = {
  props: PROPS,
  emits: [ 'closeClicked' ],
  setup,
  template: TEMPLATE
};

export default StaffSidebarPermissions;
