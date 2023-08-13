const PROPS = {
  division: Object,
  divisionHierarchy: Array
};

const TEMPLATE = `
  <div class="UI-table UI-table-heading" :id="division.name.replace(' ', '_')">
    <div class="UI-table-row event-color-secondary">
      <div class="UI-center UI-table-cell-no-border">{{division.name}}</div>
      <div class="UI-center UI-table-cell-no-border">
        <staff-section-nav :divisionContent=divisionHierarchy :id="division.name.replace(' ', '_') + '_nav'"></staff-section-nav>
      </div>
      <div class="UI-table-cell-no-border">{{division.email.join('<br/>')}}</div> 
    </div>
  </div>
`;

const staffDivisionComponent = {
  props: PROPS,
  template: TEMPLATE
};


export default staffDivisionComponent;
