<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php

include_once($FUNCTIONDIR.'/volunteer.inc');
$data = get_volunteer_hours_for_user();
if (!empty($data)) {
    echo implode(array_keys($data[0]), ',');
    echo "<br />\n";
    foreach ($data as $record) {
        echo implode($record, ',');
        echo "<br />\n";
    }
}

?>
