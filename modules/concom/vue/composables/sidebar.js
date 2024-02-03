/* globals Vue */

function addDepartmentView(data) {
  const { division } = data;
  const sidebarData = {
    isDivision: division == null,
    parentDepartment: division
  }

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
