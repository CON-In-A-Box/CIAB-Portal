/* globals Vue */
const PROPS = {
  staff: Object,
  isDepartment: Boolean,
};

const TEMPLATE = `
  <div class="UI-table-cell-no-border">{{staff.departmentName}}</div>
  <div class="UI-table-cell-no-border" v-if="isDepartment">{{ staffDivisionName(staff).value }}</div>
  <div class="UI-table-cell-no-border">{{ staffFullName(staff).value }}</div>
  <div class="UI-table-cell-no-border">{{staff.pronouns}}</div>
  <div class="UI-table-cell-no-border">{{ staffPosition(staff, isDepartment).value }}</div>
  <div class="UI-table-cell-no-border" v-if="isDepartment">{{staff.email}}</div>
  <div class="UI-table-cell-no-border">
    <p>{{staff.note}}</p>
    <p v-if="currentUser?.id === staff.id">This is you!</p>
  </div>
  <div class="UI-table-cell-no-border">
  </div>
`;

const staffFullName = (staff) => Vue.computed(() => {
  return `${staff.firstName} ${staff.lastName}`;
});

const staffDivisionName = (staff) => Vue.computed(() => {
  return staff.divisionName ?? staff.departmentName;
});

const staffPosition = (staff, isDepartment) => Vue.computed(() => {
  if (!isDepartment) {
    if (staff.position === 'Head') {
      return 'Director';
    } else if (staff.position === 'Specialist') {
      return 'Support';
    }
  }

  return staff.position;
});

const INITIAL_DATA = () => {
  return {
    staffFullName,
    staffDivisionName,
    staffPosition
  }
}

const departmentMemberComponent = {
  props: PROPS,
  template: TEMPLATE,
  setup() {
    const currentUser = Vue.inject('currentUser');
    return {
      currentUser: currentUser.value
    }
  },
  data: INITIAL_DATA
};

export default departmentMemberComponent;
