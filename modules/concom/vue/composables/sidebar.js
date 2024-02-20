/* globals Vue */

function addDepartmentView(data) {
  const { division } = data;
  const sidebarData = {
    isDivision: division == null,
    parentId: division?.id
  };

  return {
    sidebarData,
    sidebarName: 'department'
  }
}

function editDepartmentView(data) {
  const { existingData } = data;
  const isDivision = existingData.departments != null;
  const sidebarData = {
    ...existingData,
    isDivision,
    subDepartments: existingData.departments?.length ?? 0,
  };

  return {
    sidebarData,
    sidebarName: 'department'
  }
}

export function useSidebar() {
  const showSidebar = Vue.ref(false);
  const sidebarName = Vue.ref(null);
  const sidebarData = Vue.ref(null);
  const storedTempData = Vue.ref([]);

  function prepareSidebar(data) {
    if (!showSidebar.value) {
      showSidebar.value = true;
    }

    if (data.eventName === 'addDepartment') {
      const viewData = addDepartmentView(data);
      sidebarName.value = viewData.sidebarName;
      sidebarData.value = viewData.sidebarData;
    } else if (data.eventName === 'editDepartment') {
      const viewData = editDepartmentView(data);
      sidebarName.value = viewData.sidebarName;
      sidebarData.value = viewData.sidebarData;
    }
  }

  function changeSidebar(data) {
    if (data.eventName === 'displayEmail') {
      storedTempData.value.push({ data: data.sidebarData, name: data.viewName });

      sidebarData.value = data.newData;
      sidebarName.value = data.newView;
    }

    if (data.eventName === 'closeEmail') {
      const tempData = storedTempData.value.pop();

      tempData.data.email.push(data.sidebarData.email);

      sidebarData.value = tempData.data;
      sidebarName.value = tempData.name;
    }
  }

  function closeSidebar() {
    showSidebar.value = false;
    sidebarName.value = null;
    sidebarData.value = null;
  }

  return { showSidebar, sidebarName, sidebarData, prepareSidebar, changeSidebar, closeSidebar };
}
