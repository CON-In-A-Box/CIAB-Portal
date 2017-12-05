<?php


function buildSessionData($accountId)
{
  // Refresh Account info from Neon to verify update
    require_once($FUNCTIONDIR."/users.inc");
    $result = pullIndividualAccount($accountId);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
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
            if (isset($val['fieldValue'])) {
                $_SESSION['customFields'][$val['fieldId']][] = $val['fieldValue'];
            } elseif (isset($val['fieldOptionId'])) {
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
    $preferredFirstName = search_definedFields('Preferred First Name');
    if (isset($_SESSION['customFields'][$preferredFirstName])) {
        $_SESSION['preferredFirstName'] = &$_SESSION['customFields'][$preferredFirstName][0];
        $_SESSION['preferredName'] = $_SESSION['preferredFirstName'];
    } else {
        $_SESSION['preferredFirstName'] = &$_SESSION['firstName'];
        $_SESSION['preferredName'] = $_SESSION['firstName'];
    }
    $_SESSION['preferredName'] .= " ";

    $preferredLastName = search_definedFields('Preferred Last Name');
    if (isset($_SESSION['customFields'][$preferredLastName])) {
        $_SESSION['preferredLastName'] = &$_SESSION['customFields'][$preferredLastName][0];
        $_SESSION['preferredName'] .= $_SESSION['preferredLastName'];
    } else {
        $_SESSION['preferredLastName'] = &$_SESSION['lastName'];
        $_SESSION['preferredName'] .= $_SESSION['lastName'];
    }

    // Current ConCom Positions and ConCom Years Served
    $_SESSION['customFields']['currConComPos'] = &$_SESSION['customFields'][search_definedFields('CVGConCom Current Position')];

    // Current Hotel Lottery Status
    $_SESSION['customFields']['hotelLotteryStatus'] = &$_SESSION['customFields'][search_definedFields('Lottery Status')];
    $_SESSION['customFields']['hotelAssignedRoom'] = &$_SESSION['customFields'][search_definedFields('Room Assigned')];

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
