<?php
// Main update process
?>
<div id="main_content" class="UI-cell-top UI-maincontent">
<h2>Preforming system upgrade</h2>
<?php

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'from'         => FILTER_SANITIZE_NUMBER_INT,
    'to'         => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    require_once($FUNCTIONDIR."/update.inc");
    db_do_update($updateData['from'], $updateData['to']);

    echo "<div>\n";
    echo "<button type='button' class='UI-eventbutton w3-left' onclick=window.location.href='http://".$_SERVER['SERVER_NAME']."'>Proceed</button>\n";
    echo "</div>\n";
}

?>
</div>
