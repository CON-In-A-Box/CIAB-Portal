<div id=page" class="w3-container">
<div id="main_content" class="w3-container w3-mobile">
<span class="w3-rest w3-center event-color-primary w3-xlarge w3-block w3-left-align w3-padding">ConCom</span>
<div id="concom" class="w3-container w3-hide w3-show w3-mobile w3-responsive">

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
</div>
</div>
</div><!--close main_content div-->
