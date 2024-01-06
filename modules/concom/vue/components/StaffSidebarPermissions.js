const TEMPLATE = `
  <div class="UI-center">
    <h2 class="UI-red">Add Permission</h2>
  </div>
  <div>
    <hr/>
    <select class="UI-select" style="width:auto" id="updated_perm_perm">
      <option>admin.sudo</option>
      <option>api.get.deadline.all</option>
      <option>api.delete.deadline.all</option>
      <option>api.post.deadline.all</option>
      <option>api.put.deadline.all</option>
      <option>asset.admin</option>
      <option>concom.reports</option>
      <option>concom.view</option>
      <option>concom.modify.all</option>
      <option>concom.add.all</option>
      <option>registration.reports</option>
      <option>site.admin</option>
      <option>site.concom.permissions</option>
      <option>site.concom.structure</option>
      <option>site.email_lists</option>
      <option>volunteers.admin</option>
      <option>volunteers.reports</option>
    </select>

    <input class="UI-hide" id="updated_perm_dept" readonly />
    <input class="UI-hide" id="updated_perm_position" readonly />
  </div>
  <div class="UI-center">
    <hr/>
    <button class="UI-eventbutton">Save</button>
    <button class="UI-yellowbutton">Close</button>
  </div>
`;

const StaffSidebarPermissions = {
  template: TEMPLATE
};

export default StaffSidebarPermissions;
