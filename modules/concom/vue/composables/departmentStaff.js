/* globals apiRequest, Vue */
import { extractDepartmentStaff } from '../../sitesupport/department-staff-parser.js'

export function useDepartmentStaff() {
  const departmentStaff = Vue.ref(null);
  const loadingDepartment = Vue.ref(false);
  const departmentError = Vue.ref(null);

  const departmentStaffPositions = Vue.ref(null);
  const loadingPositions = Vue.ref(false);
  const departmentPositionsError = Vue.ref(null);

  async function fetchDepartmentStaff(departmentId) {
    if (departmentId === -1) {
      // Don't need to call the API, department is new.
      return;
    }

    loadingDepartment.value = true;

    try {
      const departmentResponse = await apiRequest('GET', `department/${departmentId}/staff?subdepartments=1&max_results=all`);
      const departmentStaffData = JSON.parse(departmentResponse.responseText);

      departmentStaff.value = extractDepartmentStaff(departmentStaffData.data);
    } catch (error) {
      departmentError.value = error;
    } finally {
      loadingDepartment.value = false;
    }
  }

  async function fetchDepartmentStaffPositions() {
    loadingPositions.value = true;

    try {
      const positionResponse = await apiRequest('GET', 'staff/positions');
      const parsedData = JSON.parse(positionResponse.responseText);

      departmentStaffPositions.value = parsedData.data;
    } catch (error) {
      departmentPositionsError.value = error;
    } finally {
      loadingPositions.value = false;
    }
  }

  return {
    departmentStaff,
    loadingDepartment,
    departmentError,
    fetchDepartmentStaff,
    departmentStaffPositions,
    loadingPositions,
    departmentPositionsError,
    fetchDepartmentStaffPositions
  };
}
