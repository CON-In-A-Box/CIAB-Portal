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

function is_true($val, $return_null=false){
    $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
    return ( $boolval===null && !$return_null ? false : $boolval );
}

class Neon 
{
  // Abstracted HTTP request, used by other class methods
  private function apiCore($method, $parameters) {
    if (isset($GLOBALS['NEONTRIAL']) && is_true($GLOBALS['NEONTRIAL'])) {
        $url = 'https://trial.z2systems.com/neonws/services/api/' . $method;
    } else {
        $url = 'https://api.neoncrm.com/neonws/services/api/' . $method;
    }
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
      $siteconfig = $GLOBALS['SITECONFIG'];
      $neonid = $GLOBALS['NEONID'];
      switch($result['errors']['error'][0]['errorCode']) {
        case "2": // NeonCRM Crashed, we can't serve this call
          sendError("Could not login to the NeonCRM System with OrgID " . $neonid . ".  Check the login/API settings in " . $siteconfig . ". Error from " . __FILE__, $result);
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
          sendError($neonid . " does not have enough permissions to make this call.  Consult with your administrator. Error from " . __FILE__, $result);
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
    $siteconfig = $GLOBALS['SITECONFIG'];
    $neonid = $GLOBALS['NEONID'];
    if (isset($neonid) && isset($GLOBALS['NEONKEY'])) {
      $method = 'common/login';
      $parameters = '&login.apiKey=' . $GLOBALS['NEONKEY'] . '&login.orgid=' . $neonid;
      $response = $this->apiCore($method, $parameters);
      if($response['operationResult'] == 'SUCCESS') {
        $this->setSession($response['userSessionId']);
        return TRUE;
      } else {
        if($response['errors']['error'][0]['errorCode'] == "2") {
          echo "NeonCRM seems to be down, please try again later.";
        } else {
          sendError("Could not login to the NeonCRM System with OrgID " . $neonid . ".  Check the login/API settings in " . $siteconfig . ". Error from " . __FILE__, $response);
          return $response;
        }
        die("Unable to connect to NeonCRM");
      }
    } else {
      sendError("NeonCRM credentials have not been set yet.  Check the login/API settings in " . $siteconfig . ".  Error From " . __FILE__, $response);
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
}

/* Start the NeonCRM Session */
$Neon = new Neon();

?>
