/*
 * Base function for a sidebar panel
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals sidebarMainDiv */
/* exported hideSidebar, showSidebar */

var currentSidebar = null;

function hideSidebar() {
  if (currentSidebar) {
    currentSidebar.classList.remove('UI-sidebar-shown');
    currentSidebar.classList.add('UI-sidebar-hidden');
    var section = document.getElementById(sidebarMainDiv);
    section.classList.add('UI-rest');
    section.classList.remove('UI-mainsection-sidebar-shown');
    currentSidebar = null;
  }
}

function showSidebar(Id) {
  if (currentSidebar) {
    hideSidebar();
  }

  currentSidebar = document.getElementById(Id);
  currentSidebar.classList.remove('UI-sidebar-hidden');
  currentSidebar.classList.add('UI-sidebar-shown');
  var section = document.getElementById(sidebarMainDiv);
  section.classList.remove('UI-rest');
  section.classList.add('UI-mainsection-sidebar-shown');
}
