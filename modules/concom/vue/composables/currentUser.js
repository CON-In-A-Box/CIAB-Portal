/* globals apiRequest, Vue */

export function useCurrentUser() {
  const user = Vue.ref(null);
  const loadingUser = Vue.ref(false);
  const userError = Vue.ref(null);

  async function fetchUser() {
    loadingUser.value = true;

    try {
      const userResponse = await apiRequest('GET', 'member');
      const userData = JSON.parse(userResponse.responseText);

      const userPermissionsResponse = await apiRequest('GET', `member/${userData.id}/permissions?max_results=all`);
      const userPermissionData = JSON.parse(userPermissionsResponse.responseText);

      user.value = {
        id: parseInt(userData.id),
        permissions: userPermissionData.data
      };
    } catch (error) {
      userError.value = error.message;
    } finally {
      loadingUser.value = false;
    }
  }

  function hasPermission(permission) {
    return user.value?.permissions?.find((item) => item.subtype === permission)?.allowed === 1;
  }

  return { user, loadingUser, userError, fetchUser, hasPermission };
}
