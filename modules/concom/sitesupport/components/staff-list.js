/* globals apiRequest */
import { extractDivisionHierarchy } from '../division-parser.js';

const TEMPLATE = `
  <staff-division v-for="division in divisionHierarchy" :division=division :divisionHierarchy=divisionHierarchy></staff-division>
`;

const INIITIAL_DATA = {
  divisionHierarchy: []
};

const fetchDivisionData = async() => {
  const response = await apiRequest('GET', 'department');
  const departmentData = JSON.parse(response.responseText);

  return extractDivisionHierarchy(departmentData.data);
};

const onMounted = async(componentInstance) => {
  const result = await fetchDivisionData();
  componentInstance.divisionHierarchy.push(...result);
};

const staffListComponent = {
  template: TEMPLATE,
  data() {
    return INIITIAL_DATA
  },
  async mounted() {
    await onMounted(this);
  }
};

export default staffListComponent;
