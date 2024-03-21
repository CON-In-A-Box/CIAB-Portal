/* globals apiRequest, Vue */

export function useDepartmentPermissions() {
  const departmentPermissions = Vue.ref(null);
  const loadingPermissions = Vue.ref(false);
  const permissionsError = Vue.ref(null);

  async function fetchDepartmentPermissions(departmentId) {
    if (departmentId === -1) {
      // Don't need to call the API, department is new.
      // Realistically, we should never get here.
      return;
    }

    loadingPermissions.value = true;

    try {
      const departmentPermissionResponse = await apiRequest('GET', `department/${departmentId}/permission`);
      const parsedData = JSON.parse(departmentPermissionResponse.responseText);

      departmentPermissions.value = parsedData.data;
    } catch (error) {
      permissionsError.value = error;
    } finally {
      loadingPermissions.value = false;
    }
  }

  return { departmentPermissions, loadingPermissions, permissionsError, fetchDepartmentPermissions };
}
