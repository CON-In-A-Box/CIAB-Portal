<div id="dynamic_content" class="main_screen">
<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>\n";

/*  if( isset($_REQUEST['DoFeed'])) {
    require_once($FUNCTIONDIR . "/divisional.inc");

    foreach(file($FUNCTIONDIR . "/directadd.feed", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
      flush();
      $feed = explode(",", $line);

      echo "Updating " . $feed[2] . " " . $feed[3] . " (" . $feed[5] . ") as " . $feed[4] . " in " . $feed[0] . ":";

      $search = [ 
        'method' => 'account/listAccounts', 
        'criteria' => [
          ['Email', 'EQUAL', $feed[5]],
        ],
        'columns' => [
          'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Preferred Name', 'Email 1'],
        ],
        'page' => [
          'currentPage' => 1,
          'pageSize' => 200,
          'sortColumn' => 'Account ID',
          'sortDirection' => 'ASC',
        ],
      ];
      $result = $GLOBALS['Neon']->search($search);

      if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
        if ( count($result['searchResults']) == 1 ) {
          $update=AddConComPosition($result['searchResults'][0]['Account ID'], $feed[0], $feed[4], $feed[6]);
          if ($update) {
            echo "Complete.<br />\n";
          } else {
            echo "FAILED - Manually check.<br />\n";
          }
        } elseif ( count($result['searchResults']) == 0 ) {
          echo "Cannot update - Missing Email address.<br />";
        } else {
          echo "Cannot update - Multiple records with this Email address.<br />";
        }
      }
    }
  }
*/
  // Display all, with links to menu items
  echo "You have to wait... this isn't ready!\n";

?>
</div><!--close main_content div-->
