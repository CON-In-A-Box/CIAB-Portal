const TEMPLATE = `
  <div class="UI-container">
    <div class="UI-maincontent">
      <div class="CONCOM-admin-content">
        <div class="CONCOM-admin-section">
          <span>ConCom Structure</span>
        </div>
        
        <div class="UI-maincontent">
          <div class="UI-container UI-padding UI-center">
            Basic Usage:
            <p>Use the <i class="fas fa-plus-square"></i> to add a new department</p>
            <p>Use the <span class="UI-yellow">Add New Division<i class="fas fa-plus-square"></i></span> button to add a new Division</p>
            <p>Drag departments around to change the division</p>
            <p>Double click on Divisions or Departments to change the properties</p>
          </div>

          <div class="UI-container UI-margin UI-center">
            <button class="UI-redbutton UI-padding UI-margin">All ConCom Site Permissions (RBAC)</button>
          </div>

          <staff-structure-division></staff-structure-division>
        </div>
      </div>
    </div>

    <staff-structure-sidebar name="edit_department_position"></staff-structure-sidebar>
    <staff-structure-sidebar name="edit_department_email"></staff-structure-sidebar>
    <staff-structure-sidebar name="edit_department_permissions"></staff-structure-sidebar>
    <staff-structure-sidebar name="add_permissions"></staff-structure-sidebar>
  </div>
`;

const StaffStructurePage = {
  template: TEMPLATE
};

export default StaffStructurePage;
