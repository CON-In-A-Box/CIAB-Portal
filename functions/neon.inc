<?php
/*
 * NeonCRM PHP API Library
 * http://github.com/z2systems/neon-php
 * Learn more about the API at http://help.neoncrm.com/api
 * Learn more about NeonCRM at http://www.z2systems.com
 * Authored by Colin Pizarek
 * http://github.com/colinpizarek
 * Pulled 12/20/2015
 */

class Neon 
{
  // Abstracted HTTP request, used by other class methods
  private function apiCore($method, $parameters) {
    $url = 'https://api.neoncrm.com/neonws/services/api/' . $method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, TRUE);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Required for WAMP only
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, TRUE);
    reset($result);
    $first_key = key($result);
    $result = $result[$first_key];
    return $result;
  }

  // Calls to NeonCRM go via this api except to login
  private function api($request) {
    // If we don't have a NeonCRM Session, get logged in
    if(!$this->getSession()) {
      $this->login();
    }
    // Set up to make the NeonCRM call
    $method = $request['method'];
    $parameters = 'responseType=json&userSessionId=' . $this->getSession();
    $parameters .= $request['parameters'];
    // Make the call
    $result = $this->apiCore($method, $parameters);
    // Check for obvious errors, maybe we timed out?
    if($result['operationResult'] != 'SUCCESS') {
      switch($result['errors']['error'][0]['errorCode']) {
        case "2": // NeonCRM Crashed, we can't serve this call
          sendError("Could not login to the NeonCRM System with OrgID " . $GLOBALS['NEONID'] . ".  Check the login/API settings in " . $GLOBALS['SITECONFIG'] . ". Error from " . __FILE__, $result);
          die("Unable to connect to NeonCRM");
        break;
        case "4": // Invalid Session, try logging in again and rerun the call
          unset($_SESSION['neonSession']);
          unset($result);
          $this->login();
          $parameters = 'responseType=json&userSessionId=' . $this->getSession(); // rebuild with new session id
          $parameters .= $request['parameters'];
          $result = $this->apiCore($method, $parameters);
        break;
        case "5": // API Permissions
          sendError($GLOBALS['NEONID'] . " does not have enough permissions to make this call.  Consult with your administrator. Error from " . __FILE__, $result);
          die("API Permissions Failure");
        break;
      }
    }
    return $result;
  }
  
  // Retrieves the session ID
  private function getSession() {
    if (isset($_SESSION['neonSession'])) {
      return $_SESSION['neonSession'];
    } else {
      return null;
    }
  }

  // Saves the session ID
  private function setSession($session) {
    $_SESSION['neonSession'] = $session;
  }

  // Executes a login and stores the Session ID.
  public function login() {
    if (isset($GLOBALS['NEONID']) && isset($GLOBALS['NEONKEY'])) {
      $method = 'common/login';
      $parameters = '&login.apiKey=' . $GLOBALS['NEONKEY'] . '&login.orgid=' . $GLOBALS['NEONID'];
      $response = $this->apiCore($method, $parameters);
      if($response['operationResult'] == 'SUCCESS') {
        $this->setSession($response['userSessionId']);
        return TRUE;
      } else {
        if($response['errors']['error'][0]['errorCode'] == "2") {
          echo "NeonCRM seems to be down, please try again later.";
        } else {
          sendError("Could not login to the NeonCRM System with OrgID " . $GLOBALS['NEONID'] . ".  Check the login/API settings in " . $GLOBALS['SITECONFIG'] . ". Error from " . __FILE__, $response);
          return $response;
        }
        die("Unable to connect to NeonCRM");
      }
    } else {
      sendError("NeonCRM credentials have not been set yet.  Check the login/API settings in " . $SITECONFIG . ".  Error From " . __FILE__, $response);
      die("CIAB Not Configured");
    }
  }

  // Hard and dirty Logout (invalidate session) from Neon
  public function logout() {
    if($this->getSession()) {
      $this->go(['method' => 'common/logout']);
      unset($_SESSION['neonSession']);
    }
  }

  /*
   * General purpose API request to be executed after login
   */
  public function go($request) {
    if (isset($request['method'])) {
      $str = null;
      if (isset($request['parameters'])) {
        $str = http_build_query($request['parameters']);
      }
      $parameters = '&' . $str;
      $build = array();
      $build['method'] = $request['method'];
      $build['parameters'] = $parameters;
      $go = $this->api($build);
      return $go;
    }
  }

  /*
   * TK Updated General purpose API request to be executed after login
   * Allows for NeonCRMs oddity with updating custom files by stripping [0-9] from field names so
   * multiple same-name parameters can be passed to the API call. - Use with Caution - Very
   * indescriminate about removing ANY pattern that matches %5B[0-9]+%5D (EG: [1] [2] [55])
   */
  public function go1($request) {
    if (isset($request['method'])) {
      $str = null;
      if (isset($request['parameters'])) {
        $str = http_build_query($request['parameters']);
        $str = preg_replace('/%5B[0-9]+%5D/simU', '', $str);
      }
      $parameters = '&' . $str;
      $build = array();
      $build['method'] = $request['method'];
      $build['parameters'] = $parameters;
      $go = $this->api($build);
      return $go;
    }
  }

  /*
   * search
   * works with listaccounts, listmemberships, listdonations, etc
   */
  public function search($request) {
    $standard = null;
    $custom = null;
    $criteria = null;
    $paging = null;
    if (isset($request['method'])) {
      if (isset($request['columns']['standardFields'])) {
        foreach ($request['columns']['standardFields'] as $std) {
          $std = str_replace(' ','%20',$std);
          $standard = $standard . '&outputfields.idnamepair.id=&outputfields.idnamepair.name=' . $std;
          }
      }
      if (isset($request['columns']['customFields'])) {
        foreach ($request['columns']['customFields'] as $cus) {
          $cus = str_replace(' ','%20',$cus);
          $custom = $custom . '&outputfields.idnamepair.name=&outputfields.idnamepair.id=' . $cus;
          }
        }
      if (isset($request['criteria'])) {
        foreach ($request['criteria'] as $crit) {
          $key = '&searches.search.key=' . $crit[0];
          $operator = '&searches.search.searchOperator=' . $crit[1];
          $value = '&searches.search.value=' . $crit[2];
          $criteria = $criteria . $key . $operator . $value;
          $criteria = str_replace(' ','%20',$criteria);
          }
        }
      if (isset($request['page']['currentPage'])) {
        $paging = $paging . '&page.currentPage=' . $request['page']['currentPage'];
        }
      if (isset($request['page']['pageSize'])) {
        $paging = $paging . '&page.pageSize=' . $request['page']['pageSize'];
        }
      if (isset($request['page']['sortColumn'])) {
        $paging = $paging . '&page.sortColumn=' . str_replace(' ','%20',$request['page']['sortColumn']);
        }
      if (isset($request['page']['sortDirection'])) {
        $paging = $paging . '&page.sortDirection=' . $request['page']['sortDirection'];
        }

      $parameters = $criteria . $standard . $custom . $paging;
      $build = array();
      $build['method'] = $request['method'];
      $build['parameters'] = $parameters;
      $go = $this->api($build);
      $go = $this->parseListRequest($go);
      return $go;
    } else {
    return null;
    }
  }

  /*
   * Parses the server response for list requests
   */
  private function parseListRequest($data) {
    $result = array();
    if ($data['operationResult'] == 'SUCCESS') {
      $people = array();
      foreach ($data['searchResults']['nameValuePairs'] as $key => $value) {
        $people[$key] = $value;
        foreach ($people as $person) {
          foreach ($person['nameValuePair'] as $pair) {
            if (isset($pair['name'])) {
              $name = $pair['name'];
            } else {
              $name = null;
            }
            if (isset($pair['value'])) {
              $value = $pair['value'];
            } else {
              $value = null;
            }
            $data['searchResults'][$key][$name] = $value;
          }
        }
      }
      unset($data['searchResults']['nameValuePairs']);
      return $data;
    } 
    else {
      return $data;
    }
  }
  
  /*
   * Rebuild all local tables from NeonCRM Data
   * This functionality should be replaced when the API can do direct searches
   * of event data and custom field data
   */
  public function rebuildNeonData() {
    ##==## Boy is this gonna be fun!
  }
  
  public function doGeneralSearch() {
    /* Retrieve and sanitize POST data */
    $arguments = [
      'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
      'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
      'To'        => FILTER_SANITIZE_SPECIAL_CHARS,
      'From'      => FILTER_SANITIZE_SPECIAL_CHARS,
      'Do'        => FILTER_SANITIZE_SPECIAL_CHARS,
      'email'     => FILTER_SANITIZE_EMAIL,
    ];
    if (!empty($_POST)) {
      $searchCriteria = filter_input_array( INPUT_POST, $arguments );
    } else {
      $searchCriteria = filter_input_array( INPUT_GET, $arguments );
    }

    // Formulate the search query
    $assignedRoomFieldId   = search_definedFields('Room Assigned');
    $overrideCatFieldId    = search_definedFields('Hotel Override Category');
    $overrideRoomFieldId   = search_definedFields('Hotel Override');
    $lotteryStatusFieldId  = search_definedFields('Lottery Status');
    $qualifierFieldId      = search_definedFields('Hotel Qualifier');
    $roomPreferenceFieldId = search_definedFields('Bed Preference');
    $checkInFieldId        = search_definedFields('Check In');
    $checkOutFieldId       = search_definedFields('Check Out');
    $other1FieldId         = search_definedFields('Other In Room 1');
    $other2FieldId         = search_definedFields('Other In Room 2');
    $other3FieldId         = search_definedFields('Other In Room 3');

    $search = [ 
      'method' => 'account/listAccounts', 
      'columns' => [
        'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1', 'City', 'State'],
        'customFields' => [
          $assignedRoomFieldId,
          $overrideCatFieldId,
          $overrideRoomFieldId,
          $lotteryStatusFieldId,
          $qualifierFieldId,
          $roomPreferenceFieldId,
          $checkInFieldId,
          $checkOutFieldId,
          $other1FieldId,
          $other2FieldId,
          $other3FieldId,
        ],
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
    }
    if ( isset( $searchCriteria['email'] ) && !empty( $searchCriteria['email'] ) ) {
      $search['criteria'][] = ['Email', 'EQUAL', $searchCriteria['email']];
    }

    // If there are search criteria present, execute the search query
    if ( !empty( $search['criteria'] ) ) {
      $result = $GLOBALS['Neon']->search($search);
      $result['message'] = 'No results match your search.';
    } else {
      $result = [];
      $result['message'] = 'You must specify search criteria.';
    }

    echo '<form action="index.php?Function=' . $_REQUEST['Function'] . '" method="POST">';
      foreach($searchCriteria as $field => $value) {
        if(!empty($value)) {
          echo '<input type=hidden name="' . $field . '" value="' . $value . '">';
        }
      }
      echo '<div class="w3-row w3-green">';
        echo '<div>';
          echo 'NeonCRM Account Search';
          echo '<button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Search</button>';
        echo '</div>';
        echo '<div class="w3-container">';
          echo '<div class="w3-half">';
            echo '<input type="text" class="w3-input w3-border" name="firstName" placeholder="First Name" value="' . htmlentities( $searchCriteria['firstName']) . '" />';
          echo '</div>';
          echo '<div class="w3-half">';
            echo '<input type="text" class="w3-input w3-border" name="lastName" placeholder="Last Name" value="' . htmlentities( $searchCriteria['lastName']) . '" />';
          echo '</div>';
          echo '<div>';
            echo '<input type="text" class="w3-input w3-border" name="email" placeholder="Email" value="' . htmlentities( $searchCriteria['email']) . '" />';
          echo '</div>';
          echo '<button class="w3-button w3-block w3-brown">Search</button>';
        echo '</div>';
      echo '</div>';
    echo '</form>';
    return $result;
  }
}

/* Start the NeonCRM Session */
$Neon = new Neon();

?>
