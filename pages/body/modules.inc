<?php

$modules = get_modules();

?>
<div id="page"  class="UI-container">
    <div id="main_content"  class="UI-maincontent">
        <h2 class="UI-event-sectionbar">Website Module Configuration</h2>
        <div class='UI-table-all' id='module_table'>
            <div class='UI-table-header'>
                <div class='UI-table-row'>
                    <div class='UI-table-cell'>Module</div>
                    <div class='UI-table-cell UI-center'>Enabled</div>
                </div>
            </div>

<?php
foreach ($modules as $k => $m) {
    if ($m === null) {
        print "<div class='UI-table-row UI-disabled'>\n";
        print " <div class='UI-table-cell'>".$k." ";
        print "<label class='UI-label' class='UI-tooltip'>(Disabled in Environment)</label>";
        print "</div>\n";
    } else {
        print "<div class='UI-table-row module-table-row' id='".$k."'>\n";
        print " <div class='UI-table-cell'>".$k."</div>\n";
        ;
    }
    print " <div class='UI-table-cell UI-center'><input class='check' id='check_".$k."' type='checkbox'";
    if ($m == true) {
        print " checked='checked' ";
    }
    print "></div>\n";
    print "</div>\n";
}
?>
        </div>
        <div class="UI-margin UI-center">
            <button class='UI-redbutton' onclick='updateModules()'>Submit</button>
        </div>
        </div>
    </div>
</div>
