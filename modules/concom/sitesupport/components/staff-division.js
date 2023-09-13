/* globals Vue */
const PROPS = {
  division: Object
};

const TEMPLATE = `
  <div class="UI-container UI-padding UI-border">
    <div class="UI-table UI-table-heading" :id="htmlTagFriendlyName(division).value">
      <div class="UI-table-row event-color-secondary">
        <div class="UI-center UI-table-cell-no-border">{{divisionName(division).value}}</div>
        <div class="UI-center UI-table-cell-no-border">
          <staff-section-nav :id="htmlTagFriendlyName(division).value + '_nav'"></staff-section-nav>
        </div>
        <div class="UI-table-cell-no-border">
          <template v-for="email in division.email">{{email}}<br/></template>
        </div> 
      </div>
    </div>
    <staff-department :department=division :division=division></staff-department>
    <div class="UI-container UI-padding">
      <template v-for="department in division.departments">
        <staff-department :department=department :division=division></staff-department>
      </template>
    </div>
  </div>
`;

const htmlTagFriendlyName = (division) => Vue.computed(() => {
  return division.name.replaceAll(' ', '_');
});

const divisionName = (division) => Vue.computed(() => {
  return division.specialDivision ? division.name : `${division.name} Division`;
});

const INITIAL_DATA = () => {
  return {
    htmlTagFriendlyName,
    divisionName
  }
};

const staffDivisionComponent = {
  props: PROPS,
  template: TEMPLATE,
  data: INITIAL_DATA
};

export default staffDivisionComponent;
