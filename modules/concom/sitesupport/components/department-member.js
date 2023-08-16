/* globals Vue */
const PROPS = {
  staff: Object,
  currentUser: Object
};

const TEMPLATE = `
  <div class="UI-table-cell-no-border">{{staff.departmentName}}</div>
  <div class="UI-table-cell-no-border" v-if="staff.divisionName !== ''">{{staff.divisionName}}</div>
  <div class="UI-table-cell-no-border">{{ staffFullName(staff) }}</div>
  <div class="UI-table-cell-no-border">{{staff.pronouns}}</div>
  <div class="UI-table-cell-no-border">{{staff.position}}</div>
  <div class="UI-table-cell-no-border">
    <p>{{staff.note}}</p>
    <p v-if="currentUser.id === staff.id">This is you!</p>
  </div>
  <div class="UI-table-cell-no-border"></div>
`;

const staffFullName = (staff) => Vue.computed(() => {
  return `${staff.firstName} ${staff.lastName}`;
});

const INITIAL_DATA = () => {
  return {
    staffFullName
  }
}

const departmentMemberComponent = {
  props: PROPS,
  template: TEMPLATE,
  data: INITIAL_DATA
};

export default departmentMemberComponent;
