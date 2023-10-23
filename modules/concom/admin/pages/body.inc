<div id=page" class="UI-container">
<script>
    var sidebarMainDiv = 'main_content';
</script>
<div id="main_content" class="UI-maincontent">
<?php
$deptCount = departmentDetails(current_eventID());
?>
<div id="admin_content" class="CONCOM-admin-content">

    <div class="CONCOM-admin-section">
        <span>ConCom Structure</span>
    </div>

<div id="admin" class="UI-maincontent UI-show">
<div class="UI-container UI-padding UI-center">Basic Usage:
<p>Use the <i class='fas fa-plus-square'></i> to add a new department</p>
<p>Use the <span class='UI-yellow'>Add New Division<i class='fas fa-plus-square'></i></span> button to add a new Division</p>
<p>Drag departments around to change the division</p>
<p>Double click on Divisions or Departments to change the properties</p>
</div>

<?php if (RBACApiRequest('site.concom.permissions')) {?>
<div id='all_rbac' class='UI-container UI-margin UI-center'>
    <button class='UI-redbutton UI-padding UI-margin' id='all_rbac'
            onclick='showRBAC("all");'>
         All ConCom Site Permissions (RBAC)
    </button>
</div>
<?php }?>

<?php
build_structure_sections();
?>

</div>
</div>
</div><!--close main_content div-->

    <div class='UI-sidebar-hidden UI-fixed' id='edit_position'>
        <div class='UI-center'>
            <h2 class='UI-red'>Position Details</h2>
        </div>
        <div>
            <hr>
            <input class="UI-hiddeninput" id="dept_id" value='-1' readonly>
            <label class='UI-label' for='position_name'>Position Name:</label>
            <input class="UI-input" id="dept_name" value=''>
            <label class='UI-label' for='dept_email'>Position Emails:</label>
            <div class='UI-border' id="dept_email">
            </div>
<?php
if (RBACApiRequest('site.concom.permissions')) {
    ?>
            <button class='UI-redbutton UI-padding UI-margin' id='dept_rbac'>
                 Position Site Permissions (RBAC)
            </button><br>
    <?php
}
?>
            <label class='UI-label' for='dept_count'>Staff Count:</label>
            <input class="UI-input" readonly id="dept_count" value=''>
            <div id="sub_dept">
                <label class='UI-label' for='dept_sub'>Sub Departments:</label>
                <input class="UI-input" readonly id="dept_sub" value=''>
            </div>
            <div id="fallback_dept">
                <label class='UI-label' for='dept_fallback'>Fallback For:</label>
                <select class="UI-select" style="width:auto" name="Fallback" id="dept_fallback">
                </select>
            </div>
            <div id="parent">
                <label class='UI-label' for='dept_parent'>Division:</label>
                <select class="UI-select" style="width:auto" name="Parent" id="dept_parent">
<?php
foreach ($Divisions as $division) {
    echo "<option value=".$Departments[$division]['id'].">$division</option>\n";
}
?>
                </select>
            </div>
        </div>
        &nbsp;
        <div>
          <div class='UI-table switch-table UI-padding UI-center' id='dept_slider_parent'>
            <div class='UI-table-row'>
              <div class='UI-table-cell'>
                <span class='UI-padding'>Department</span>
                <label class=switch><input type="checkbox" class=toggle
                    id=dept_slider onclick='toggleDept();'>
                  <div class=slider></div>
                </label>
                <span class='UI-padding'>Division</span>
              </div>
            </div>
          </div>
        <div class='UI-center'>
            <hr>
            <button class='UI-eventbutton'
                onclick='savePosition();'>
              Save
            </button>
            <button class='UI-yellowbutton'
                onclick='hideSidebar();'>
              Close
            </button>
            <button class='UI-redbutton' id='delete_btn'
                onclick='deletePosition();'>
              Delete
            </button>
        </div>
      </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='edit_email'>
        <div class='UI-center'>
            <h2 class='UI-red'>Position Email</h2>
        </div>
        <div>
            <hr>
            <label class='UI-label' for='email_email'>EMail:</label>
            <input class="UI-input" id="email_email"
            oninput='changeEmail();'>
            <input class="UI-hide" id="email_original" readonly>
            <input class="UI-hide" id="email_alias" readonly>
            <input class="UI-hide" id="email_index" readonly>
            <input class="UI-hide" id="email_dept" readonly>
        </div>
        <div class='UI-center'>
            <hr>
            <button class='UI-eventbutton'
                id='email_save_btn' onclick='saveEmail();'>
              Save
            </button>
            <button class='UI-yellowbutton'
                onclick='returnPosition();'>
              Close
            </button>
            <button class='UI-redbutton' id='email_delete_btn'
                onclick='deleteEmail();'>
              Delete
            </button>
        </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='edit_rbac'>
        <div class='UI-center'>
            <h2 class='UI-red' id='rbac_title'>Position Permissions</h2>
        </div>
        <div>
            <label class='UI-label' for='inherited'>Inherited Permissions:</label>
            <div class='UI-border' id="inherited">
            </div>
            <label class='UI-label' for='present'>Department Permissions:</label>
            <div class='UI-border' id="position">
            </div>
        </div>
        <div class='UI-center'>
            <button class='UI-yellowbutton'
                onclick='returnPosition();'>
              Close
            </button>
        </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='add_ac'>
        <div class='UI-center'>
            <h2 class='UI-red'>Add Permission</h2>
        </div>
        <div>
            <hr>
            <select class="UI-select" style="width:auto" id="perm_perm">
            </select>

            <input class="UI-hide" id="perm_dept" readonly>
            <input class="UI-hide" id="perm_position" readonly>
        </div>
        <div class='UI-center'>
            <hr>
            <button class='UI-eventbutton'
                id='ac_perm_save_btn' onclick='savePermission();'>
              Save
            </button>
            <button class='UI-yellowbutton'
                onclick='returnRBAC();'>
              Close
            </button>
        </div>
    </div>

</div>
