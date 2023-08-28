/* globals apiRequest, Vue */
import { extractDivisionHierarchy } from '../division-parser.js';

const TEMPLATE = `
  <div class="UI-maincontent">
    <div class="UI-event-sectionbar">ConCom</div>
    <div class="UI-maincontent">
      <staff-division v-for="division in divisions" :division=division></staff-division>
    </div>
  </div>
`;

const fetchDivisionData = async() => {
  const response = await apiRequest('GET', 'department');
  const departmentData = JSON.parse(response.responseText);

  return extractDivisionHierarchy(departmentData.data);
};

const fetchCurrentUser = async() => {
  const response = await apiRequest('GET', 'member');
  const memberData = JSON.parse(response.responseText);

  return {
    id: parseInt(memberData.id)
  };
}

const onMounted = async(componentInstance) => {
  const result = await fetchDivisionData();
  componentInstance.divisions.push(...result);

  const userResult = await fetchCurrentUser();
  componentInstance.currentUser = userResult;
};

const staffListComponent = {
  template: TEMPLATE,
  setup() {
    const currentUser = Vue.ref({ id: null });
    Vue.provide('currentUser', Vue.readonly(currentUser));

    const divisions = Vue.ref([]);
    Vue.provide('divisions', Vue.readonly(divisions));
    return {
      currentUser,
      divisions
    }
  },
  async mounted() {
    await onMounted(this);
  }
};

export default staffListComponent;
