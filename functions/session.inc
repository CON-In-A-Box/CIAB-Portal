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


// Support DeepLinks by testing
function validateDeepLink($deepLinkId)
{
    if ($deepLinkId == $GLOBALS['DUMPSECURE']) {
        return(true);
    } else {
    // Do search for DeepLink Info based on Function
    // Search customfields where pending function equals DeepLinkId - Should never return more than one
    }

}


function loadDefinedFields()
{
    global $_SESSION, $Neon, $ADMINACCOUNTS;

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Account",
        ],
        ];
    $result = $Neon->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $_SESSION['definedFields']['customField'] = $result['customFields']['customField'];
        foreach ($result['customFields']['customField'] as $val) {
            $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
            if (isset($val['fieldOptions'])) {
                foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                    $_SESSION['definedFields'][$fval['id']] = $fval['name'];
                }
                unset($fval);
                if ($val['fieldName'] == "Lottery Status") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Lottery Status'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Bed Preference") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Bed Preference'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Hotel Qualifier") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Hotel Qualifier'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                }
            }
        }
        unset($val);
    } else {
        die("Impossible error during Defined Custom Fields Download");
    }

}


function loadAccount($accountId)
{
    global $_SESSION, $ADMINACCOUNTS;

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

    // If the logged in user is in the volunteers, note it
    if (class_exists('\\concom\\VOLUNTEERS') && method_exists('\\concom\\VOLUNTEERS', 'inVolunteers')) {
        $_SESSION['IS_VOLUNTEERS'] = concom\VOLUNTEERS::inVolunteers($accountId);
    }

    // If the logged in user is a System Admin (defined in the config file), note it
    if (in_array($accountId, $ADMINACCOUNTS)) {
        $_SESSION['IS_ADMIN'] = true;
    }

}
