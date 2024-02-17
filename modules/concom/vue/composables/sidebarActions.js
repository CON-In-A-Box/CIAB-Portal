/* globals apiRequest */

async function saveDepartment(data) {
  const { sidebarData } = data;

  let params = `Name=${sidebarData.departmentName}`;
  if (sidebarData.fallbackDepartment?.id != null) {
    params += `&FallbackID=${sidebarData.fallbackDepartment.id}`;
  }

  if (sidebarData.parentDepartment?.id != null) {
    params += `&ParentID=${sidebarData.parentDepartment.id}`;
  }

  await apiRequest('POST', 'department', params);
}


export function useSidebarActions() {
  async function saveSidebarData(data) {
    if (data.eventName === 'saveDepartment') {
      await saveDepartment(data);
    }
  }

  return { saveSidebarData };
}
