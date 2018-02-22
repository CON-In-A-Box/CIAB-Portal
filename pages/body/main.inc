<?php require($PAGESDIR.'/base/leftnav.inc');

$homepage_panes = [];
$modules = scandir($MODULESDIR);
foreach ($modules as $key => $value) {
    if (!in_array($value, array(".", ".."))) {
        if (in_array($value, $DISABLEDMODULES)) {
            continue;
        }
        if (is_dir($MODULESDIR . DIRECTORY_SEPARATOR . $value)) {
            if (is_file($MODULESDIR . DIRECTORY_SEPARATOR . $value . DIRECTORY_SEPARATOR . 'pages/panes.inc')) {
                require_once($MODULESDIR . DIRECTORY_SEPARATOR . $value.DIRECTORY_SEPARATOR.'pages/panes.inc');
            }
        }
    }
}
?>

<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php
echo '<div class="w3-container w3-center">';
echo '<b>Welcome '.$_SESSION['preferredName'].'!</b>';
echo "</div>\n";

foreach($homepage_panes as $drawfunc) { $drawfunc(); }

?>

</div><!--close main_content div-->
