<?php
/*.
      require_module 'standard';
.*/

require_once($FUNCTIONDIR."/authentication.inc");
require_once($FUNCTIONDIR."/users.inc");

if (!empty($_POST)) {
    // Retrieve and sanitize POST data
    $arguments = [
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
    'addressLine1'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'addressLine2'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'city'               => FILTER_SANITIZE_SPECIAL_CHARS,
    'state'              => FILTER_SANITIZE_SPECIAL_CHARS,
    'zipCode'            => FILTER_SANITIZE_SPECIAL_CHARS,
    'zipPlus4'           => FILTER_SANITIZE_NUMBER_INT,
    'country'            => FILTER_SANITIZE_NUMBER_INT,
    'province'           => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['email1'])) {
        $password = rppg();
        if (array_key_exists('Neon', $GLOBALS)) {
            $account = neon_createUser(
                $updateData['email1'],
                $updateData['firstName'],
                $updateData['lastName']
            );
            if ($account) {
                lookup_user_by_id($account);
            }
            $updateData['username'] = $updateData['email1'];
            $updateData['password'] = $password;
        } else {
            $account = createUser($updateData['email1'], 1000);
        }
        if (!$account) {
            header('HTTP/1.0 401 Unauthorized');
            echo "HTTP/1.0 401 Unauthorized";
            exit();
        }
        $updateData['accountId'] = $account;
        updateAccount($updateData);
        if (!reset_password($updateData['email1'], $password)) {
            header('HTTP/1.0 404 Not Found');
            echo "HTTP/1.0 404 Not Found";
        }
        exit();
    }
}
