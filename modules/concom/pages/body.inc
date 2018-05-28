<div id="main_content" class="w3-cell w3-cell-top w3-mobile">
<?php
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
</div><!--close main_content div-->
