<div id=page" class="w3-container">
<script>
    var sidebarMainDiv = 'main_content';
</script>
<div id="main_content" class="w3-container w3-mobile">
<?php

if (isset($_SESSION['IS_ADMIN'])) {
    if (!empty($_REQUEST)) {
        // Retrieve and sanitize POST data
        $arguments = [
        'admin'         => FILTER_SANITIZE_INT,
        ];
        $updateData = filter_input_array(INPUT_GET, $arguments);
        $in_admin = $updateData['admin'];
    }

    $deptCount = departmentDetails();

?>
<div id="admin_content" class="w3-section w3-container">

    <button onclick="expandSection('admin')" class="w3-rest w3-center w3-red w3-xlarge w3-button w3-block w3-left-align">
        <span>ConCom Structure</span>
<?php
if (!$in_admin) {
    echo '<i id="admin_arrow" class="fa fa-caret-down"></i>';
} else {
    echo '<i id="admin_arrow" class="fa fa-caret-up"></i>';
}
?>
    </button>
<?php
if (!$in_admin) {
    echo '<div id="admin" class="w3-container w3-hide w3-mobile w3-responsive">';
} else {
    echo '<div id="admin" class="w3-container w3-hide w3-mobile w3-responsive w3-show">';
}
?>

<div class="w3-container w3-padding w3-center">Basic Usage:
<p>Use the <i class='fa fa-plus-square'></i> to add a new department</p>
<p>Use the <span class='w3-yellow'>Add New Division<i class='fa fa-plus-square'></i></span> button to add a new Division</p>
<p>Drag departments around to change the division</p>
<p>Double click on Divisions or Departments to change the properties</p>
</div>

<?php
build_structure_sections();
?>

</div>
</div>
<?php
}

if (isset($_SESSION['IS_ADMIN'])) {
?>
    <div id="concom_content" class="w3-section w3-container">
    <button onclick="expandSection('concom')" class="w3-rest w3-center event-color-primary w3-xlarge w3-button w3-block w3-left-align">
                <span>ConCom</span> <i id="concom_arrow" class="fa fa-caret-up"></i>
            </button>
<?php
} else {
    echo '<span class="w3-rest w3-center event-color-primary w3-xlarge w3-block w3-left-align w3-padding">ConCom</span>';
}
echo '<div id="concom" class="w3-container w3-hide w3-show w3-mobile w3-responsive">';

require_once(__DIR__."/../functions/concom.inc");

// if this is an update, but we don't have the right permissions, ignore it.
if (isset($_REQUEST['AddDepartment'])) {
    if (isset($_REQUEST['memberId'])) {
        if (!isset($_REQUEST['Position'])) {
            // We have someone, lets get their position
            doConComAddAssign($_REQUEST['AddDepartment'], $_REQUEST['memberId']);
        }
    } else {
        // begin search
        doConComAddSearch($_REQUEST['AddDepartment']);
    }
} else {
    ConComListDisplay();
}
?>
</div>
</div>
</div><!--close main_content div-->

    <div class='w3-hide w3-padding' style="display: inline-block; position: fixed;" id='edit_position'>
        <div class='w3-center'>
            <h2 class='w3-red'>Position Details</h2>
        </div>
        <div>
            <hr>
            <input class="w3-input w3-disabled w3-hide" id="dept_id" value='-1' readonly>
            <label for='position_name'>Position Name:</label>
            <input class="w3-input w3-border" id="dept_name" value=''>
            <label for='dept_count'>Staff Count:</label>
            <input class="w3-input w3-border" readonly id="dept_count" value=''>
            <div id="sub_dept">
                <label for='dept_sub'>Sub Departments:</label>
                <input class="w3-input w3-border" readonly id="dept_sub" value=''>
            </div>
            <div id="parent">
                <label for='dept_parent'>Division:</label>
                <select class="w3-select w3-border" style="width:auto" name="Parent" id="dept_parent">
<?php
foreach ($Divisions as $division) {
    echo "<option value=".$Departments[$division]['id'].">$division</option>\n";
}
?>
                </select>
            </div>
        </div>
          <div
          <table class='switch-table w3-padding w3-center' id='dept_slider_parent'><tr><td>
            <span class='w3-padding'>Department</span>
            <label class=switch><input type="checkbox" class=toggle
                id=dept_slider onclick='toggleDept();'>
              <div class=slider></div></label></td><td><span class='w3-padding'>Division</span></td></tr>
          </table>
        </div>

       <div class='w3-center'>
            <hr>
            <button class='w3-button w3-round-xxlarge event-color-primary'
                onclick='savePosition();'>
              Save
            </button>
            <button class='w3-button w3-round-xxlarge w3-yellow'
                onclick='hideSidebar();'>
              Close
            </button>
            <button class='w3-button w3-round-xxlarge w3-red' id='delete_btn'
                onclick='deletePosition();'>
              Delete
            </button>
        </div>
    </div>
</div>
