var currentSidebar = null;

function hideSidebar() {
  if (currentSidebar) {
    currentSidebar.classList.add('w3-hide');
    currentSidebar.classList.remove('w3-quarter');
    var section = document.getElementById(sidebarMainDiv);
    section.classList.add('w3-rest');
    section.classList.remove('w3-threequarter');
    currentSidebar = null;
  }
}

function showSidebar(Id) {
  if (currentSidebar) {
    hideSidebar(sidebarMainDiv);
  }

  currentSidebar = document.getElementById(Id);
  currentSidebar.classList.remove('w3-hide');
  currentSidebar.classList.add('w3-quarter');
  var section = document.getElementById(sidebarMainDiv);
  section.classList.remove('w3-rest');
  section.classList.add('w3-threequarter');
}
