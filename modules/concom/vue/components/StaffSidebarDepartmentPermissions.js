const PROPS = {
  edit: Boolean
}

const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Position Permissions</h2>
  </div>
  <div>
    <label class="UI-label" for="updated_inherited">Inherited Permissions:</label>
    <div id="updated_inherited" class="UI-border">
      <span><b>Head: </b>
        <span>site.concom.structure</span> <span>site.concom.permissions</span>
      </span><br/>
      <span><b>Sub-Head: </b></span><br/>
      <span><b>Specialist: </b></span><br/>
    </div>
    <label class="UI-label" for="updated_present">Department Permissions:</label>
    <div id="updated_position" class="UI-border">
      <span><b>Head: </b>
        <span><button class="UI-roundbutton"><i class="fas fa-plus-square"></i></button></span>
      </span><br/>
      <span><b>Sub-Head: </b>
        <span><button class="UI-roundbutton"><i class="fas fa-plus-square"></i></button></span>
      </span><br/>
      <span><b>Specialist: </b>
        <span><button class="UI-roundbutton"><i class="fas fa-plus-square"></i></button></span>
      </span><br/>
    </div>
  </div>
  <div class="UI-center">
    <button class="UI-yellowbutton">Close</button>
  </div>
`;

const StaffSidebarDepartmentPermissions = {
  props: PROPS,
  template: TEMPLATE
};

export default StaffSidebarDepartmentPermissions;
