/* globals Vue, showSpinner, hideSpinner */
import { useDepartmentPermissions } from '../composables/departmentPermissions.js';
import { useDepartmentStaff } from '../composables/departmentStaff.js';

const PROPS = {
  data: Object
}

const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Position Permissions</h2>
  </div>
  <div>
    <label class="UI-label" for="updated_inherited">Inherited Permissions:</label>
    <div id="updated_inherited" class="UI-border">
      <template v-for="(entry, key) in positionMap">
        <span>
          <b>{{ entry }}: </b>
          <template v-for="(permissions, permissionKey) in inheritedPermissions">
            <template v-if="permissionKey === key">
              <span v-for="item in permissions">{{ item.permission }}&nbsp;</span>
            </template>
          </template>
        </span><br/>
      </template>
    </div>
    <label class="UI-label" for="updated_present">Department Permissions:</label>
    <div id="updated_position" class="UI-border">
      <template v-for="(entry, key) in positionMap">
        <span>
          <b>{{ entry }}: </b>
          <template v-for="(permissions, permissionKey) in permissions">
            <template v-if="permissionKey === key">
              <span v-for="item in permissions">
                <span><button class="UI-roundbutton"><i class="fas fa-minus-square"></i></button></span>
                {{ item.permission }}&nbsp;
              </span>
            </template>
          </template>
          <span><button class="UI-roundbutton"><i class="fas fa-plus-square"></i></button></span>
        </span><br/>
      </template>
    </div>
  </div>
  <div class="UI-center">
    <button class="UI-yellowbutton" @click="$emit('closeClicked')">Close</button>
  </div>
`;

function reducePermissions(permissions) {
  return permissions.reduce((acc, current) => {
    if (acc[current.position] == null) {
      acc[current.position] = [];
    }

    acc[current.position].push({ id: current.id, permission: current.name });
    return acc;
  }, {});
}

function setup(props) {
  const departmentId = props.data?.departmentId ?? 'all';
  const inheritedPermissions = Vue.ref({});
  const permissions = Vue.ref({});
  const positionMap = Vue.ref({});

  const { departmentStaffPositions, loadingPositions, fetchDepartmentStaffPositions } = useDepartmentStaff();
  const { departmentPermissions, loadingPermissions, fetchDepartmentPermissions } = useDepartmentPermissions();

  Vue.watch(departmentStaffPositions, () => {
    (departmentStaffPositions.value || []).forEach((position) => {
      positionMap.value[position.id] = position.position;
    });
  });

  Vue.watch(departmentPermissions, () => {
    if (departmentPermissions.value?.length > 0) {
      if (departmentId !== 'all') {
        const globalPermissions = departmentPermissions.value.filter((permission) => permission.departmentId === 'all');
        inheritedPermissions.value = reducePermissions(globalPermissions);

        const specificPermissions = departmentPermissions.value.filter((permission) => parseInt(permission.departmentId) === departmentId);
        permissions.value = reducePermissions(specificPermissions);
      } else {
        permissions.value = reducePermissions(departmentPermissions.value);
      }
    }
  });

  const loadingValues = [loadingPermissions, loadingPositions];

  Vue.watch([loadingPermissions, loadingPositions], () => {
    loadingValues.some((item) => item.value) ? showSpinner() : hideSpinner();
  })

  return {
    departmentId,
    inheritedPermissions,
    permissions,
    positionMap,
    departmentStaffPositions,
    fetchDepartmentPermissions,
    fetchDepartmentStaffPositions
  }
}

async function onCreated() {
  await this.fetchDepartmentStaffPositions();
  await this.fetchDepartmentPermissions(this.departmentId);
}

const StaffSidebarDepartmentPermissions = {
  props: PROPS,
  emits: [ 'closeClicked' ],
  setup,
  created: onCreated,
  methods: {
    reducePermissions
  },
  template: TEMPLATE
};

export default StaffSidebarDepartmentPermissions;
