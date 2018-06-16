<?php
/*.
      require_module 'standard';
.*/

require_once($FUNCTIONDIR.'/users.inc');
require_once($FUNCTIONDIR.'/authentication.inc');

if (!empty($_POST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'accountId'          => FILTER_SANITIZE_NUMBER_INT,
    'contactId'          => FILTER_SANITIZE_NUMBER_INT,
    'addressId'          => FILTER_SANITIZE_NUMBER_INT,
    'firstName'          => FILTER_SANITIZE_SPECIAL_CHARS,
    'middleName'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'lastName'           => FILTER_SANITIZE_SPECIAL_CHARS,
    'suffix'             => FILTER_SANITIZE_SPECIAL_CHARS,
    'preferredFirstName' => FILTER_SANITIZE_SPECIAL_CHARS,
    'preferredLastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'email1'             => FILTER_SANITIZE_EMAIL,
    'email2'             => FILTER_SANITIZE_EMAIL,
    'email3'             => FILTER_SANITIZE_EMAIL,
    'phone1'             => FILTER_SANITIZE_SPECIAL_CHARS,
    'phone2'             => FILTER_SANITIZE_SPECIAL_CHARS,
    'conComDisplayPhone' => FILTER_SANITIZE_SPECIAL_CHARS,
    'addressLine1'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'addressLine2'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'city'               => FILTER_SANITIZE_SPECIAL_CHARS,
    'state'              => FILTER_SANITIZE_SPECIAL_CHARS,
    'zipCode'            => FILTER_SANITIZE_SPECIAL_CHARS,
    'zipPlus4'           => FILTER_SANITIZE_NUMBER_INT,
    'country'            => FILTER_SANITIZE_NUMBER_INT,
    'province'           => FILTER_SANITIZE_SPECIAL_CHARS,
    'auth'               => FILTER_SANITIZE_SPECIAL_CHARS,
    'password'           => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    if (!empty($_POST)) {
        $updateData = filter_input_array(INPUT_POST, $arguments);
    } else {
        $updateData = filter_input_array(INPUT_GET, $arguments);
    }

    if (isset($updateData['password'])) {
        if (check_authentication(
            $_SESSION['accountId'],
            $updateData['auth'],
            false
        )) {
            set_password($_SESSION['accountId'], $updateData['password']);
            exit();
        } else {
            header('HTTP/1.0 403 Forbidden');
            echo "HTTP/1.0 403 Forbidden";
            exit();
        }
    }

    $result = updateAccount($updateData);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        require_once($FUNCTIONDIR."/session.inc");
        // Refresh Account info from Neon
        buildSessionData($_SESSION['accountId']);
    } else {
        sendError("Profile Update Failed", $result);
    }
}
