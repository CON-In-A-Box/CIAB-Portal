/* globals apiRequest, Vue */

export function useDepartmentEmail() {
  const departmentEmail = Vue.ref([]);
  const loadingDepartmentEmail = Vue.ref(false);
  const departmentEmailError = Vue.ref(null);

  async function fetchDepartmentEmail(departmentId) {
    if (departmentId === -1) {
      // Don't need to call the API, department is new.
      return;
    }

    loadingDepartmentEmail.value = true;

    try {
      const departmentEmailResponse = await apiRequest('GET', `department/${departmentId}/email`);
      const parsedResponse = JSON.parse(departmentEmailResponse.responseText)
      departmentEmail.value = parsedResponse.data;
    } catch (error) {
      departmentEmailError.value = error;
    } finally {
      loadingDepartmentEmail.value = false;
    }
  }

  return { departmentEmail, loadingDepartmentEmail, departmentEmailError, fetchDepartmentEmail };
}
