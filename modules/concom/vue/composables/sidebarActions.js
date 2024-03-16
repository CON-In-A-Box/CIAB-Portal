/* globals apiRequest */

function getDepartmentParams(sidebarData) {
  let params = `Name=${sidebarData.departmentName}`;
  if (sidebarData.fallbackDepartment?.id != null) {
    params += `&FallbackID=${sidebarData.fallbackDepartment.id}`;
  }

  if (sidebarData.parentDepartment?.id != null) {
    params += `&ParentID=${sidebarData.parentDepartment.id}`;
  }

  return params;
}

async function saveDepartment(data) {
  const { sidebarData } = data;
  const params = getDepartmentParams(sidebarData);

  await apiRequest('POST', 'department', params);
}

async function updateDepartment(data) {
  const { sidebarData } = data;
  const params = getDepartmentParams(sidebarData);

  await apiRequest('PUT', `department/${sidebarData.id}`, params);
}

async function deleteDepartment(data) {
  const { sidebarData } = data;

  await apiRequest('DELETE', `department/${sidebarData.id}`);
}

async function saveEmail(data) {
  const { sidebarData } = data;

  await apiRequest('POST', 'email', `Email=${sidebarData.email}&DepartmentID=${sidebarData.departmentId}`);
}

async function updateEmail(data) {
  const { sidebarData } = data;

  await apiRequest('PUT', `email/${sidebarData.id}`, `Email=${sidebarData.email}`);
}

async function deleteEmail(data) {
  const { sidebarData } = data;

  await apiRequest('DELETE', `email/${sidebarData.id}`);
}


export function useSidebarActions() {
  async function saveSidebarData(data) {
    if (data.eventName === 'saveDepartment') {
      if (data.sidebarData?.id > 0) {
        await updateDepartment(data);
      } else {
        await saveDepartment(data);
      }
    }

    if (data.eventName === 'saveEmail') {
      if (data.sidebarData?.id > 0) {
        await updateEmail(data);
      } else {
        await saveEmail(data);
      }
    }
  }

  async function deleteSidebarData(data) {
    if (data.eventName === 'deleteDepartment') {
      await deleteDepartment(data);
    }

    if (data.eventName === 'deleteEmail') {
      await deleteEmail(data);
    }
  }

  return { saveSidebarData, deleteSidebarData };
}
