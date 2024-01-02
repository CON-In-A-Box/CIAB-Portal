const PROPS = {
  edit: Boolean
}

const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Position Details</h2>
  </div>
  <div>
    <hr/>
    <input class="UI-hiddeninput" id="updated_dept_id" value="-1" readonly />
    <label class="UI-label" for="updated_position_name">Position Name:</label>
    <input class="UI-input" id="updated_dept_name" value=""/>
    <label class="UI-label" for="updated_dept_email">Position Emails:</label>
    <div class="UI-border" id="updated_dept_email">
      <button class="UI-roundbutton">test@test-con.org</button>
      <br/>
      <button class="UI-roundbutton"><i class="fas fa-plus-square"</i></button>
    </div>

    <button class="UI-redbutton UI-padding UI-margin" id="updated_dept_rbac">
      Position Site Permissions (RBAC)
    </button><br/>

    <label class="UI-label" for="updated_dept_count">Staff Count:</label>
    <input class="UI-input" id="updated_dept_count" value="" readonly />
    <div id="updated_sub_dept">
      <label class="UI-label" for="updated_dept_sub">Sub Departments:</label>
      <input class="UI-input" id="updated_dept_sub" value="" readonly />
    </div>
    <div id="updated_fallback_dept">
      <label class="UI-label" for="updated_dept_fallback">Fallback For:</label>
      <select class="UI-select" style="width:auto" name="Fallback" id="updated_dept_fallback"></select>
    </div>
    <div id="updated_parent">
      <label class="UI-label" for="updated_dept_parent">Division:</label>
      <select class="UI-select" style="width:auto" name="Parent" id="updated_dept_parent">
        <option value="">Activities</option>
        <option value="">Administration</option>
        <option value="">CFO Staff</option>
        <option value="">External Relations and Communications</option>
        <option value="">Facilities</option>
        <option value="">Hospitality</option>
        <option value="">Productions</option>
        <option value="">Systems</option>
        <option value="">Committees</option>
        <option value="">Corporate Staff</option>
      </select>
    </div>
  </div>
  <div>
    <div id="updated_dept_slider_parent" class="UI-table switch-table UI-padding UI-center">
      <div class="UI-table-row">
        <div class="UI-table-cell">
          <span class="UI-padding">Department</span>
          <label class="switch">
            <input id="updated_dept_slider" class="toggle" type="checkbox" />
            <div class="slider"></div>
          </label>
          <span class="UI-padding">Division</span>
        </div>
      </div>
    </div>
    <div class="UI-center">
      <hr/>
      <button class="UI-eventbutton>Save</button>
      <button class="UI-yellowbutton>Close</button>
      <button class="UI-redbutton">Delete</button>
    </div>
  </div>
`;

const StaffSidebarDepartment = {
  props: PROPS,
  template: TEMPLATE
};

export default StaffSidebarDepartment;
