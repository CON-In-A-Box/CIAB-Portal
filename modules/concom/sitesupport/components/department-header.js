/* globals Vue */
const PROPS = {
  isDepartment: Boolean,
  department: Object
}

const TEMPLATE = `
  <div :class="headerClass(isDepartment).value" :id="'table_header_' + htmlTagFriendlyName(department).value">
    <div :class="columnClass(isDepartment).value" v-if="isDepartment">Department</div>
    <div :class="columnClass(isDepartment).value" v-if="isDepartment">Division</div>
    <div :class="columnClass(isDepartment).value" v-if="!isDepartment">Division</div>
    <div :class="columnClass(isDepartment).value">Name</div>
    <div :class="columnClass(isDepartment).value">Pronouns</div>
    <div :class="columnClass(isDepartment).value">Position</div>
    <div :class="columnClass(isDepartment).value" v-if="isDepartment">Email</div>
    <div :class="columnClass(isDepartment).value">Note</div>
    <div :class="columnClass(isDepartment).value"></div>
  </div>
`;

const htmlTagFriendlyName = (department) => Vue.computed(() => {
  return department.name.replaceAll(' ', '_');
});

const headerClass = (isDepartment) => Vue.computed(() => {
  return isDepartment ? 'CONCOM-list-department-header-row' : 'CONCOM-list-division-header-row';
});

const columnClass = (isDepartment) => Vue.computed(() => {
  return isDepartment ? 'CONCOM-list-department-header-column' : 'CONCOM-list-division-header-column';
});

const departmentHeaderComponent = {
  props: PROPS,
  template: TEMPLATE,
  methods: {
    htmlTagFriendlyName,
    headerClass,
    columnClass
  }
};

export default departmentHeaderComponent;
