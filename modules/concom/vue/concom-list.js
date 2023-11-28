/* globals Vue, showSpinner, hideSpinner */
import staffListComponent from './staff-list.js';
import sectionNavComponent from './staff-section-nav.js';
import staffDivisionComponent from './staff-division.js';
import departmentHeaderComponent from './department-header.js';
import departmentMemberComponent from './department-member.js';
import staffDepartmentComponent from './staff-department.js';
import staffSidebarComponent from './staff-sidebar.js';
import lookupuser from '../../../sitesupport/vue/lookupuser.js';
import staffDivisionVisualComponent from './staff-division-visual.js';

function updateLoading(value) {
  this.isLoading = value;
  this.isLoading ? showSpinner() : hideSpinner();
}

const staffApp = Vue.createApp({
  setup() {
    const isLoading = Vue.ref(false);
    Vue.provide('isLoading', {
      isLoading,
      updateLoading
    });

    return {
      isLoading
    }
  },
  methods: {
    updateLoading
  }
});

staffApp.component('staff-division', staffDivisionComponent);
staffApp.component('staff-section-nav', sectionNavComponent);
staffApp.component('staff-list', staffListComponent);
staffApp.component('department-header', departmentHeaderComponent);
staffApp.component('department-member', departmentMemberComponent);
staffApp.component('staff-department', staffDepartmentComponent);
staffApp.component('staff-sidebar', staffSidebarComponent);
staffApp.component('lookup-user', lookupuser);
staffApp.component('org-donut', staffDivisionVisualComponent);

staffApp.mount('#staff-app');

export default staffApp;
