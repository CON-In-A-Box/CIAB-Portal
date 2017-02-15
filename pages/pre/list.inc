<?php
  if (empty( $_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
  }

  if(isset($_REQUEST['Remove']) && isset($_REQUEST['Department']) && isset($_REQUEST['Position'])) {
    require_once($FUNCTIONDIR . "/divisional.inc");
    if(isset($_SESSION['IS_ADMIN']) || (isset($_SESSION['DIR_OF']) && in_array(getDivision($_REQUEST['Department']), $_SESSION['DIR_OF'])) || (isset($_SESSION['HEAD_OF']) && in_array($_REQUEST['Department'], $_SESSION['HEAD_OF']))) {
      RemoveConComPosition($_REQUEST['Remove'], $_REQUEST['Department'], $_REQUEST['Position']);
      goSite('index.php?Function=list');
    }
  }

?>

