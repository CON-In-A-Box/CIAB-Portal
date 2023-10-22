/* globals Vue */
const TEMPLATE = `
  <div class="CONCOM-list-navigation-dropdown">
    Go To Section
    <div class="CONCOM-list-navigation-dropdown-contents">
      <a href="#main_nav" class="CONCOM-list-navigation-dropdown-content-item">Top of Page<br/></a>
      <a :href="'#' + htmlTagFriendlyName(division).value" class="CONCOM-list-navigation-dropdown-content-item" 
        v-for="division in divisionContent">{{division.name}}<br/></a>
    </div>
  </div>
`;

const htmlTagFriendlyName = (division) => Vue.computed(() => {
  return division.name.replaceAll(' ', '_');
});

const INITIAL_DATA = () => {
  return {
    htmlTagFriendlyName
  }
};

const sectionNavComponent = {
  template: TEMPLATE,
  setup() {
    const divisions = Vue.inject('divisions');
    return {
      divisionContent: divisions.value
    }
  },
  data: INITIAL_DATA
};

export default sectionNavComponent;
