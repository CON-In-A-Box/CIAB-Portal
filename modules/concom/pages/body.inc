<div id=page" class="UI-container">
<div id="main_content" class="UI-maincontent">
<div class="UI-event-sectionbar">ConCom</div>
<div id="donut"></div>
<div id="concom" class="UI-maincontent">

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
?>
<script>
var data = <?php print json_encode(buildDonutData());?>;

drawDonutChart(window.innerWidth-50, window.innerHeight-50, data, '#donut');
</script>
<?php
ConComListDisplay();
}
?>
</div>
</div>
</div><!--close main_content div-->
