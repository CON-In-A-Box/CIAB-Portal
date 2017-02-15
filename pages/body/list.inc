<div id="dynamic_content" class="main_screen">
<?php
require($PAGESDIR . '/base/leftnav.inc');
?>
<div id="main_content">
<?php
require_once($FUNCTIONDIR . "/divisional.inc");

// if this is an update, but we don't have the right permissions, ignore it.
if(isset($_REQUEST['AddDepartment'])) {
  if(isset($_SESSION['IS_ADMIN']) || (isset($_SESSION['DIR_OF']) && in_array(getDivision($_REQUEST['AddDepartment']), $_SESSION['DIR_OF'])) || (isset($_SESSION['HEAD_OF']) && in_array($_REQUEST['AddDepartment'], $_SESSION['HEAD_OF']))) {
    if(isset($_REQUEST['accountId'])) {
      if(isset($_REQUEST['Position'])) {
        // apply change
        if(empty($_REQUEST['note'])) {
          $note = '';
        } else {
          $note = $_REQUEST['note'];
        }
        AddConComPosition($_REQUEST['accountId'], $_REQUEST['AddDepartment'], $_REQUEST['Position'], $note);
        goSite('index.php?Function=list');
      } else {
        // We have someone, lets get their position
        doConComAddAssign($_REQUEST['AddDepartment'], $_REQUEST['accountId'], $_REQUEST['Name']);
      }
    } else {
      // begin search
      doConComAddSearch($_REQUEST['AddDepartment']);
    }
  } else {
    // Made a request without the right cedentials - Reset just in case
    goSite();
  }
} else {
  ConComListDisplay();
}

?>
</div><!--close main_content div-->
