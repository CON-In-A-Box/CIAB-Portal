<?php
/*.
      require_module 'standard';
.*/

require_once($FUNCTIONDIR."/session.inc");

if (!empty($_POST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'reloadProfile' => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['reloadProfile'])) {
        buildSessionData($_SESSION['accountId']);
    }
}
