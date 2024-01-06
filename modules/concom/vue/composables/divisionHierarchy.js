/* globals apiRequest, Vue */
import { extractDivisionHierarchy } from '../../sitesupport/division-parser.js';

export function useDivisionHierarchy() {
  const divisions = Vue.ref(null);
  const loadingDivisions = Vue.ref(false);
  const divisionsError = Vue.ref(null);

  async function fetchDivisions() {
    loadingDivisions.value = true;

    try {
      const divisionsResponse = await apiRequest('GET', 'department');
      const divisionData = JSON.parse(divisionsResponse.responseText);

      divisions.value = extractDivisionHierarchy(divisionData.data);
    } catch (error) {
      divisionsError.value = error.message;
    } finally {
      loadingDivisions.value = false;
    }
  }

  return { divisions, loadingDivisions, divisionsError, fetchDivisions };
}
