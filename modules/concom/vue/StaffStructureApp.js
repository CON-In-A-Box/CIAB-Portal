/* globals Vue */
import StaffStructurePage from './views/StaffStructurePage.js';
import StaffStructureDivision from './components/StaffStructureDivision.js';
import StaffSidebarDepartment from './components/StaffSidebarDepartment.js';
import StaffSidebarDepartmentPermissions from './components/StaffSidebarDepartmentPermissions.js';
import StaffSidebarPermissions from './components/StaffSidebarPermissions.js';
import StaffStructureSidebar from './components/StaffStructureSidebar.js';
import StaffSidebarEmail from './components/StaffSidebarEmail.js';

const StaffStructureApp = Vue.createApp({});
StaffStructureApp.component('staff-structure-page', StaffStructurePage);
StaffStructureApp.component('staff-structure-division', StaffStructureDivision);
StaffStructureApp.component('staff-sidebar-email', StaffSidebarEmail);
StaffStructureApp.component('staff-sidebar-department', StaffSidebarDepartment);
StaffStructureApp.component('staff-sidebar-department-permissions', StaffSidebarDepartmentPermissions);
StaffStructureApp.component('staff-sidebar-permissions', StaffSidebarPermissions);
StaffStructureApp.component('staff-structure-sidebar', StaffStructureSidebar);

StaffStructureApp.mount('#staff-structure-app');

export default StaffStructureApp;
