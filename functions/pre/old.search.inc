<?php
  if (!empty($_REQUEST['searchOn'])) {
    switch ($_REQUEST['searchOn']) {
      case "OnText":
        $Results = array(
          "text" => mysql_real_escape_string($_REQUEST['log_text_search'])
        );
        break;
      case "OnID":
        $Results = array(
          "ID" => mysql_real_escape_string($_REQUEST['log_id_search'])
        );
        break;
      case "ByTime":
        $Results = array(
          "time" => mysql_real_escape_string($_REQUEST['hours_search'])
        );
        break;
      case "ByTags":
        if (!empty($_REQUEST['search_activetag'])) {
          $activetest=array_sum($_REQUEST['search_activetag']);
        } else {
          $activetest=0;
        }
        switch ($activetest) {
          case 0:
          case 3:
            $activetag="EITHER";
            break;
          case 1:
            $activetag=TRUE;
            break;
          case 2:
            $activetag=NULL;
            break;
        }
        if (!empty($_REQUEST['search_activetag'])) {
          $tags=array_sum($_REQUEST['search_tags']);
        } else {
          $tags=0;
        }
        $Results = array(
          "tags" => $tags,
          "excl" => NULL,
          "dept" => NULL,
          "active" => $activetag,
          "reverse" => NULL
        );
        break;
      case "ALL":
        $Results = array(
          "tags" => NULL,
          "excl" => NULL,
          "dept" => NULL,
          "active" => "EITHER",
          "reverse" => NULL
        );
        break;
    }
  }
?>