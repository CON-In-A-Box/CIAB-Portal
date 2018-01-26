<div id="main_content" class="w3-cell w3-cell-top w3-mobile">
<h2>Preforming system upgrade</h2>
<?php

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'next'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);
    echo "<div>\n";
    echo "<button type='button' class='w3-button w3-green w3-left' onclick=window.location.href='http://".$_SERVER['SERVER_NAME']."/index.php?Function=".$updateData['next']."'> Proceed to ".$updateData['next']."</button>\n";
    echo "</div>\n";
}

require_once($FUNCTIONDIR."/update.inc");
db_do_update();
?>
</div>
