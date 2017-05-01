<?php
  if (empty( $_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
  }

  require_once($FUNCTIONDIR . "/divisional.inc");
  if(isset($_REQUEST['Remove']) && isset($_REQUEST['Department']) && isset($_REQUEST['Position'])) {
    if(isset($_SESSION['IS_ADMIN']) || (isset($_SESSION['DIR_OF']) && in_array(getDivision($_REQUEST['Department']), $_SESSION['DIR_OF'])) || (isset($_SESSION['HEAD_OF']) && in_array($_REQUEST['Department'], $_SESSION['HEAD_OF']))) {
      // Retrieve and sanitize POST data
      $arguments = [
        'Remove'      => FILTER_SANITIZE_NUMBER_INT,
        'Department'  => FILTER_SANITIZE_SPECIAL_CHARS,
        'Position'    => FILTER_SANITIZE_SPECIAL_CHARS,
      ];
      $removeData = filter_input_array( INPUT_POST|INPUT_GET, $arguments );
      RemoveConComPosition($removeData['Remove'], $removeData['Department'], $removeData['Position']);
      goSite('index.php?Function=list#' . $removeData['Department']);
    } else {
      // Made a request without the right cedentials - Reset just in case
      goSite();
    }
  }

  function doConComAddSearch ($department) {
    echo '<h3>Add someone to ' . $department . '</h3>';
    
    // Retrieve and sanitize POST data
    $arguments = [
      'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
      'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
      'email'     => FILTER_SANITIZE_EMAIL,
    ];
    $searchCriteria = filter_input_array( INPUT_POST|INPUT_GET, $arguments );

    // Formulate the search query
    $search = [ 
      'method' => 'account/listAccounts', 
      'columns' => [
        'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1', 'City', 'State'],
      ],
      'page' => [
        'currentPage' => 1,
        'pageSize' => 200,
        'sortColumn' => 'Last Name',
        'sortDirection' => 'ASC',
      ],
    ];

    // Some search criteria are variable based on our POST data. Add them to the query if applicable
    if ( isset( $searchCriteria['firstName'] ) && !empty( $searchCriteria['firstName'] ) ) {
      $search['criteria'][] = ['First Name', 'EQUAL', $searchCriteria['firstName']];
    }
    if ( isset( $searchCriteria['lastName'] ) && !empty( $searchCriteria['lastName'] ) ) {
      $search['criteria'][] = ['Last Name', 'EQUAL', $searchCriteria['lastName']];
      $search['page']['sortColumn'] = 'First Name';
    }
    if ( isset( $searchCriteria['email'] ) && !empty( $searchCriteria['email'] ) ) {
      $search['criteria'][] = ['Email', 'EQUAL', $searchCriteria['email']];
    }

    // If there are search criteria present, execute the search query
    if ( !empty( $search['criteria'] ) ) {
      $result = $GLOBALS['Neon']->search($search);
      $message = '<h3>No results match your search.</h3>';
    } else {
      $result = null;
      $message = '<h3>You must specify at least one of the above search criteria.</h3>';
    }

    echo '<form action="index.php?Function=list" method="POST" class="form-inline">';
    echo '<input type="hidden" name="AddDepartment" value="' . $department . '">' . "\n";
    echo '<fieldset><legend>Search the NeonCRM Database</legend>';
    echo '<p>';
    echo 'First Name';
    echo '<input type="text" class="form-control" name="firstName" value="' . htmlentities($searchCriteria['firstName']) . '"> ';
    echo 'Last Name';
    echo '<input type="text" class="form-control" name="lastName" value="' . htmlentities($searchCriteria['lastName']) . '"> ';
    echo 'Email';
    echo '<input type="text" class="form-control" name="email" value="' . htmlentities($searchCriteria['email']) . '"> ';
    echo "</p>\n";
    echo '<input type="submit" value="Search" class="btn btn-default">';
    echo '</fieldset>';
    echo "</form>\n";
    echo '<hr>';

    if(isset($result['page']['totalResults']) && $result['page']['totalResults'] >= 1) {
      echo '<table class="w3-table w3-striped w3-bordered">';
      echo '<tr><th>Name</th><th>Email</th><th>Location</th></tr>' . "\n";
      foreach($result['searchResults'] as $r) {
        echo '<tr>';
        echo '<td><a href="index.php?Function=list&AddDepartment=' . $department . '&accountId=' . $r['Account ID'] . '&Name=' . $r['First Name'] . ' ' . $r['Last Name'] . '">' . $r['First Name'] . ' ' . $r['Last Name'] . '</a></td>';
        echo '<td>' . $r['Email 1'] . '</td>';
        echo '<td>' . $r['City'] . ' ' . $r['State'] . '</td>';
        echo "</tr>\n";
      }
      echo "</table>\n";
    } else {
      echo '<p>' . $message . '</p>';
    }
  }

  function doConComAddAssign ($department, $accountid, $name) {
    echo '<h3>Add ' . $name . ' to ' . $department . '</h3>';
    echo '<form action="index.php?Function=list" method="POST" class="form-inline">';
    echo '<input type="hidden" name="AddDepartment" value="' . $department . '">' . "\n";
    echo '<input type="hidden" name="accountId" value="' . $accountid . '">' . "\n";
    echo '<fieldset><legend>Assign ' . $name . ' to ConCom</legend>';
    echo '<p>';
    echo 'Position';
    echo '<select name="Position">';
    if(getDivision($department) == $department) {
      if(isset($_SESSION['IS_ADMIN'])) {
        echo '<option value="Director">Division Director</option>';
      }
      echo '<option value="Specialist">Divisional Support</option>';
    } else {
      if(isset($_SESSION['IS_ADMIN']) || isset($_SESSION['DIR_OF'])) {
        echo '<option value="Head">Head</option>';
      }
      echo '<option value="Sub-Head" selected>Sub-Head</option>';
      echo '<option value="Specialist">Specialist</option>';
    }
    echo "</select>\n";
    echo 'Note';
    echo '<input type=text name="note"></input>';
    echo "</p>\n";
    echo '<input type="submit" value="Assign to ConCom" class="btn btn-default">';
    echo '</fieldset>';
    echo "</form>\n";
  }

  // if this is an update, but we don't have the right permissions, ignore it.
  if(isset($_REQUEST['AddDepartment'])) {
    if(isset($_SESSION['IS_ADMIN']) || (isset($_SESSION['DIR_OF']) && in_array(getDivision($_REQUEST['AddDepartment']), $_SESSION['DIR_OF'])) || (isset($_SESSION['HEAD_OF']) && in_array($_REQUEST['AddDepartment'], $_SESSION['HEAD_OF']))) {
      if(isset($_REQUEST['accountId'])) {
        if(isset($_REQUEST['Position'])) {
          // apply change
          // Retrieve and sanitize POST data
          $arguments = [
            'accountId'      => FILTER_SANITIZE_NUMBER_INT,
            'AddDepartment'  => FILTER_SANITIZE_SPECIAL_CHARS,
            'Position'       => FILTER_SANITIZE_SPECIAL_CHARS,
            'note'           => FILTER_SANITIZE_SPECIAL_CHARS,
          ];
          $addData = filter_input_array( INPUT_POST|INPUT_GET, $arguments, TRUE );
          AddConComPosition($addData['accountId'], $addData['AddDepartment'], $addData['Position'], $addData['note']);
          goSite('index.php?Function=list#' . $addData['AddDepartment']);
        }
      }
    } else {
      // Made a request without the right cedentials - Reset just in case
      goSite();
    }
  }
?>
