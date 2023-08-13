const PROPS = {
  divisionContent: Array
};

const TEMPLATE = `
  <div class="CONCOM-goto-dropdown">
    Go To Section
    <div class="CONCOM-goto-dropdown-content">
      <a href="#main_nav" class="UI-border">Top of Page<br/></a>
      <a :href="'#' + division.name.replace(' ', '_')" class="UI-border" v-for="division in divisionContent">{{division.name}}<br/></a>
    </div>
  </div>
`;

const sectionNavComponent = {
  props: PROPS,
  template: TEMPLATE
};

export default sectionNavComponent;
