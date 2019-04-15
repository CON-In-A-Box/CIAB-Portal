<div id=page" class="UI-container">
<script>
    var sidebarMainDiv = 'main_content';
</script>
<div id="main_content" class="UI-maincontent">
<?php
$deptCount = departmentDetails();
?>
<div id="admin_content" class="w3-section UI-container">

    <div class="UI-admin-sectionbar w3-block w3-left-align">
        <span>ConCom Structure</span>
    </div>

<div id="admin" class="UI-maincontent w3-show">
<div class="UI-container w3-padding UI-center">Basic Usage:
<p>Use the <i class='fa fa-plus-square'></i> to add a new department</p>
<p>Use the <span class='UI-yellow'>Add New Division<i class='fa fa-plus-square'></i></span> button to add a new Division</p>
<p>Drag departments around to change the division</p>
<p>Double click on Divisions or Departments to change the properties</p>
</div>

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
            <label for='position_name'>Position Name:</label>
            <input class="UI-input" id="dept_name" value=''>
            <label for='position_email'>Position Emails:</label>
            <div class='UI-border' id="dept_email">
            </div>
            <label for='dept_count'>Staff Count:</label>
            <input class="UI-input" readonly id="dept_count" value=''>
            <div id="sub_dept">
                <label for='dept_sub'>Sub Departments:</label>
                <input class="UI-input" readonly id="dept_sub" value=''>
            </div>
            <div id="parent">
                <label for='dept_parent'>Division:</label>
                <select class="UI-select" style="width:auto" name="Parent" id="dept_parent">
<?php
foreach ($Divisions as $division) {
    echo "<option value=".$Departments[$division]['id'].">$division</option>\n";
}
?>
                </select>
            </div>
        </div>
          <div
          <table class='switch-table w3-padding UI-center' id='dept_slider_parent'><tr><td>
            <span class='w3-padding'>Department</span>
            <label class=switch><input type="checkbox" class=toggle
                id=dept_slider onclick='toggleDept();'>
              <div class=slider></div></label></td><td><span class='w3-padding'>Division</span></td></tr>
          </table>
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

    <div class='UI-sidebar-hidden UI-fixed' id='edit_email'>
        <div class='UI-center'>
            <h2 class='UI-red'>Position Email</h2>
        </div>
        <div>
            <hr>
            <label for='email_email'>EMail:</label>
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

</div>
