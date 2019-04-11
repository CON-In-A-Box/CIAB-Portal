<div id="page" class="w3-cell-row w3-padding UI-maincontent">
<?php
/*.
  require_module 'standard';
.*/

require($PAGESDIR.'/base/leftnav.inc');

$homepage_panes = [];
$modules = scandir($MODULESDIR);
foreach ($modules as $key => $value) {
    if (!in_array($value, array(".", ".."))) {
        if (in_array($value, $DISABLEDMODULES)) {
            continue;
        }
        if (is_dir($MODULESDIR.DIRECTORY_SEPARATOR.$value)) {
            if (is_file($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'pages/panes.inc')) {
                require_once($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'pages/panes.inc');
            }
        }
    }
}
?>

<div id="main_content" class="w3-padding w3-cell w3-cell-top w3-mobile">

<?php
echo '<div class="UI-sectionbar">';
echo '<b>Welcome '.$_SESSION['preferredName'].'!</b>';
echo "</div>\n";

if (isset($_SESSION['duplicates'])) {
    echo '<p class="UI-container UI-center w3-border UI-red w3-large">';
    echo "This e-mail has ".count($_SESSION['duplicates'])." duplicate "."accounts. IDs:[".implode(", ", $_SESSION['duplicates'])."]. "."This is only displaying information for one of these Accounts."."<br />"."<b>Please</b> contact IT to have this situation corrected.<br />"."</p>";
}

foreach ($homepage_panes as $drawfunc) {
    $drawfunc();
}

?>

</div><!--close main_content div-->
</div>
