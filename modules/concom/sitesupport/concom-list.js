/* globals Vue, showSpinner, hideSpinner, Vuetify */
import staffListComponent from './components/staff-list.js';
import sectionNavComponent from './components/staff-section-nav.js';
import staffDivisionComponent from './components/staff-division.js';
import departmentHeaderComponent from './components/department-header.js';
import departmentMemberComponent from './components/department-member.js';
import staffDepartmentComponent from './components/staff-department.js';
import staffSidebarComponent from './components/staff-sidebar.js';
import lookupuser from '../../../sitesupport/vue/lookupuser.js';
import staffDivisionVisualComponent from './components/staff-division-visual.js';

function updateLoading() {
  this.isLoading = !this.isLoading;
  this.isLoading ? showSpinner() : hideSpinner();
}

const staffStandardTheme = {
  dark: false,
  colors: {
    primary: '#620272',
    secondary: '#00e500',
    error: '#B00020',
    info: '#2196F3',
    success: '#4CAF50',
    warning: '#FB8C00'
  }
}

const vuetify = Vuetify.createVuetify({
  theme: {
    defaultTheme: 'staffStandardTheme',
    themes: {
      staffStandardTheme
    }
  },
  defaults: {
    VBtn: {
      color: 'primary'
    },
    VSelect: {
      variant: 'outlined'
    },
    VListItem: {
      'color': 'white'
    }
  }
});

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
}).use(vuetify);

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
