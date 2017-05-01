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

$_SESSION['neonSession'] = null;

class Neon 
{
  /*
   * Abstracted HTTP request, used by other class methods
   */
  private function api($request) {
    $method = $request['method'];
    $parameters = $request['parameters'];
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
    return $result;
  }
  
  /*
   * Retrieves the session ID
   */
  private function getSession() {
    if (isset($_SESSION['neonSession'])) {
      return $_SESSION['neonSession'];
    } else {
      return null;
    }
  }
  
  /*
   * Saves the session ID
   */
  private function setSession($session) {
    $_SESSION['neonSession'] = $session;
  }
  
  /*
   * Executes a login and stores the Session ID.
   */
  public function login($keys) {
    if (isset($keys['orgId']) && isset($keys['apiKey'])) {
      $request = array();
      $request['method'] = 'common/login';
      $request['parameters'] = '&login.apiKey=' . $keys['apiKey'] . '&login.orgid=' . $keys['orgId'];
      $response = $this->api($request);
      reset($response);
      $first_key = key($response);
      $response = $response[$first_key];
      if ($response['operationResult'] == 'SUCCESS') {
        $this->setSession($response['userSessionId']);
        return $response;
      } 
      else {
        return $response;
      } 
    } 
    else {
      return null;
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
      $parameters = 'responseType=json&userSessionId=' . $this->getSession() . '&' . $str;
      $build = array();
      $build['method'] = $request['method'];
      $build['parameters'] = $parameters;
      $go = $this->api($build);
      reset($go);
      $first_key = key($go);
      $go = $go[$first_key];
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
      $parameters = 'responseType=json&userSessionId=' . $this->getSession() . '&' . $str;
      $build = array();
      $build['method'] = $request['method'];
      $build['parameters'] = $parameters;
      $go = $this->api($build);
      reset($go);
      $first_key = key($go);
      $go = $go[$first_key];
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

      $addon = 'responseType=json&userSessionId=' . $this->getSession();
      $parameters = $addon . $criteria . $standard . $custom . $paging;
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
    reset($data);
    $first_key = key($data);
    $data = $data[$first_key];
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
}

/* Start the NeonCRM Session */
$Neon = new Neon();
$keys = [
  'orgId'=>$NEONID,
  'apiKey'=>$NEONKEY,
  ]; 
$result = $Neon->login($keys);

if ($result['operationResult'] != 'SUCCESS') {
  echo "Failed to login to the NeonCRM System with OrgID " . $NEONID . ".  Check the login/API settings in " . $SITECONFIG . "!  Error returned: ";
  echo "<pre>";
    print_r($result);
  echo "</pre>";
  die();
}

// Support DeepLinks by testing
function validateDeepLink($deepLinkId) {
  if($deepLinkId == $GLOBALS['DUMPSECURE']) {
    return(TRUE);
  } else {
    // Do search for DeepLink Info based on Function
    // Search customfields where pending function equals DeepLinkId - Should never return more than one
  }
}

function buildSessionData($accountId) {
  // Refresh Account info from Neon to verify update
  $request = [
    'method' => 'account/retrieveIndividualAccount',
    'parameters' => [
      'accountId' => $accountId,
    ],
  ];
  
  $result = $GLOBALS['Neon']->go($request);
  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    $_SESSION['accountInfo'] = $result;
    $_SESSION['contactId'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['contactId'];
    $_SESSION['firstName'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['firstName'];
    $_SESSION['lastName'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['lastName'];
    $_SESSION['addr1'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine1'];
    $_SESSION['addr2'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine2'];
    $_SESSION['city']  = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['city'];
    $_SESSION['state'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['state']['code'];
    $_SESSION['zip']   = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCode'];
    $_SESSION['email'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['email1'];
    $_SESSION['phone'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'];

    // Parse the custom data so we can use it easily
    unset($_SESSION['customFields']); // Start with an empty set
    foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $val) {
      if ( isset( $val['fieldValue'] )) {
        $_SESSION['customFields'][$val['fieldId']][] = $val['fieldValue'];
      } elseif ( isset( $val['fieldOptionId'] )) {
        $_SESSION['customFields'][$val['fieldId']][] = $val['fieldOptionId'];
      } else {
        die("Impossible error during Account Custom fields formatting");
      }
    }
    unset($val);
  } else {
    die("Impossible error during Account Lookup");
  }

  // If we got here, then we are golden.  Map the custom fields for easy use
  // Support Preferred name
  $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
  if (isset($_SESSION['customFields'][$preferredFirstName])) {
    $_SESSION['preferredFirstName'] = &$_SESSION['customFields'][$preferredFirstName][0];
    $_SESSION['preferredName'] = $_SESSION['preferredFirstName'];
  } else {
    $_SESSION['preferredFirstName'] = &$_SESSION['firstName'];
    $_SESSION['preferredName'] = $_SESSION['firstName'];
  }
  $_SESSION['preferredName'] .= " ";

  $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);
  if (isset($_SESSION['customFields'][$preferredLastName])) {
    $_SESSION['preferredLastName'] = &$_SESSION['customFields'][$preferredLastName][0];
    $_SESSION['preferredName'] .= $_SESSION['preferredLastName'];
  } else {
    $_SESSION['preferredLastName'] = &$_SESSION['lastName'];
    $_SESSION['preferredName'] .= $_SESSION['lastName'];
  }
  
  // Current ConCom Positions and ConCom Years Served
  $_SESSION['customFields']['currConComPos'] = &$_SESSION['customFields'][array_search('CVGConCom Current Position', $_SESSION['definedFields'])];

  // Current Hotel Lottery Status
  $_SESSION['customFields']['hotelLotteryStatus'] = &$_SESSION['customFields'][array_search('Lottery Status', $_SESSION['definedFields'])];
  $_SESSION['customFields']['hotelAssignedRoom'] = &$_SESSION['customFields'][array_search('Room Assigned', $_SESSION['definedFields'])];
  
}
?>
