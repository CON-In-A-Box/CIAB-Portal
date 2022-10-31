<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/users.inc");


function buildSessionData($accountId)
{
  // Refresh Account info to verify update
    $result = pullIndividualAccount($accountId);
    if ($result !== null) {
        $_SESSION['accountInfo'] = $result;
        $_SESSION['firstName'] = &$_SESSION['accountInfo']['FirstName'];
        $_SESSION['lastName'] = &$_SESSION['accountInfo']['LastName'];
        $_SESSION['addr1'] = &$_SESSION['accountInfo']['AddressLine1'];
        $_SESSION['addr2'] = &$_SESSION['accountInfo']['AddressLine2'];
        $_SESSION['city']  = &$_SESSION['accountInfo']['AddressCity'];
        $_SESSION['state'] = &$_SESSION['accountInfo']['AddressState'];
        $_SESSION['zip']   = &$_SESSION['accountInfo']['AddressZipCode'];
        $_SESSION['email'] = &$_SESSION['accountInfo']['Email'];
        $_SESSION['phone'] = &$_SESSION['accountInfo']['Phone'];

        $_SESSION['deceased'] = &$_SESSION['accountInfo']['Deceased'];
        $_SESSION['DoNotContact'] = &$_SESSION['accountInfo']['DoNotContact'];
        $_SESSION['EmailOptOut'] = &$_SESSION['accountInfo']['EmailOptOut'];
        $_SESSION['Birthdate'] = &$_SESSION['accountInfo']['Birthdate'];
        $_SESSION['Gender'] = &$_SESSION['accountInfo']['Gender'];
        $_SESSION['preferredFirstName'] = &$_SESSION['accountInfo']['PreferredFirstName'];
        $_SESSION['preferredLastName'] = &$_SESSION['accountInfo']['PreferredLastName'];
    } else {
        die("Impossible error during Account Lookup");
    }

  // If we got here, then we are golden.  Map the custom fields for easy use
  // Support Preferred name
    if (isset($_SESSION['preferredFirstName'])) {
        $_SESSION['preferredName'] = $_SESSION['preferredFirstName'];
    } else {
        $_SESSION['preferredFirstName'] = &$_SESSION['firstName'];
        $_SESSION['preferredName'] = $_SESSION['firstName'];
    }
    $_SESSION['preferredName'] .= " ";

    if (isset($_SESSION['preferredLastName'])) {
        $_SESSION['preferredName'] .= $_SESSION['preferredLastName'];
    } else {
        $_SESSION['preferredLastName'] = &$_SESSION['lastName'];
        $_SESSION['preferredName'] .= $_SESSION['lastName'];
    }

    // Current ConCom Positions and ConCom Years Served
    if (class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
        $_SESSION['customFields']['currConComPos']  = concom\POSITION::getConComPosition($accountId);
    }

}


// Support DeepLinks for function responses
function validateDeepLink($function, $deepLinkId, $auth)
{

    // Allow only approved DeepLink Functions - Need to make this an array search
    $implementedDeeplinks = ["concom/dumplist", "concom/pastcred"];
    if (in_array($function, $implementedDeeplinks)) {
        if ($deepLinkId == "NULL" && $auth == $GLOBALS['DUMPSECURE']) {

            // This is an approved automation and secure code
            return true;
        } else {
            $sql = "SELECT * from `DeepLinks` where LinkID = $deepLinkId";
            $result = DB::run($sql);
            $value = $result->fetch();
            if ($value !== false && $auth == $value['Auth']) {
                return $value;
            }
        }
    } else {
        return false; // either unimplmented function or a Bad linkID or Authcode - Fail
    }

}


function loadAccount($accountId)
{
    global $ADMINACCOUNTS;

    buildSessionData($accountId);

    // If the logged in user is in the volunteers, note it
    if (class_exists('\\concom\\VOLUNTEERS') && method_exists('\\concom\\VOLUNTEERS', 'inVolunteers')) {
        $_SESSION['IS_VOLUNTEERS'] = concom\VOLUNTEERS::inVolunteers($accountId);
    }

    // If the logged in user is a System Admin (defined in the config file), note it
    if (in_array($accountId, $ADMINACCOUNTS)) {
        $_SESSION['IS_ADMIN'] = true;
    }

    $sql = <<<SQL
            SELECT
                cf.*,
                a.Value as Value
            FROM
                `ConfigurationField` cf
            LEFT JOIN `AccountConfiguration` a ON
                a.Field = cf.Field AND a.AccountId = $accountId
            WHERE
                cf.TargetTable = 'AccountConfiguration'
SQL;
    $result = DB::run($sql);
    $data = $result->fetchAll();
    foreach ($data as $entry) {
        if ($entry['Value'] === null) {
            $value = $entry['InitialValue'];
        } else {
            $value = $entry['Value'];
        }
        $GLOBALS[$entry['Field']] = $value;
    }

}
