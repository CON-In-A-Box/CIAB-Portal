/* globals apiRequest */
import { extractDivisionHierarchy } from '../division-parser.js';

const TEMPLATE = `
  <staff-division v-for="division in divisionHierarchy" :division=division 
    :divisionHierarchy=divisionHierarchy :currentUser=currentUser></staff-division>
`;

const INITIAL_DATA = {
  divisionHierarchy: [],
  currentUser: undefined
};

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
  componentInstance.divisionHierarchy.push(...result);

  const userResult = await fetchCurrentUser();
  componentInstance.currentUser = {
    ...userResult
  };
};

const staffListComponent = {
  template: TEMPLATE,
  data() {
    return INITIAL_DATA
  },
  async mounted() {
    await onMounted(this);
  }
};

export default staffListComponent;
