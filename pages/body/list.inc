<div id="dynamic_content" class="main_screen">
<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content">
<?php

function ConComListBuildSearch($page) {
  $conComFieldId = array_search('CVGConCom Current Position', $_SESSION['definedFields']);

  $search = [ 
    'method' => 'account/listAccounts', 
    'criteria' => [
      [$conComFieldId, 'NOT_BLANK', ''],
    ],
    'columns' => [
      'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Preferred Name', 'Email 1'],
      'customFields' => [$conComFieldId],
    ],
    'page' => [
      'currentPage' => $page,
      'pageSize' => 200,
      'sortColumn' => 'Account ID',
      'sortDirection' => 'ASC',
    ],
  ];
  $result = $GLOBALS['Neon']->search($search);

  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    $return['pages'] = $result['page']['totalPage'];
    foreach( $result['searchResults'] as $val ) {
      $positionsHeld = explode("|", $val['CVGConCom Current Position']);
      foreach( $positionsHeld as $pos) {
        $tmp = explode(",", $pos);
        $line = [
          'Department'     => $tmp[0], //Make call for Division
          'Position'       => $tmp[1],
          'First Name'     => $val['First Name'],
          'Preferred Name' => $val['Preferred Name'],
          'Last Name'      => $val['Last Name'],
          'Email'          => $val['Email 1'],
        ];
        
        if (isset($tmp[2])) {
          $line['Note'] = $tmp[2];
        } else {
          $line['Note'] = "";
        }
        
        $return['list'][] = $line;
      }
      unset($tmp);
      unset($line);
      unset($pos);
      unset($positionsHeld);
    }
    unset($val);
  } else {
    die("Could not retrieve ConComList");
  }
  return $return;
}

$result = ConComListBuildSearch(1);
echo "<pre>\n";
print_r($result);
echo "</pre>\n";

?>
</div><!--close main_content div-->
