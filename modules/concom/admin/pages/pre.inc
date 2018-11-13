<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

if (!isset($_SESSION['IS_ADMIN'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

require_once(__dir__."/../../functions/concom.inc");

$arguments = [
'modify' => FILTER_SANITIZE_STRING,
'reparent' => FILTER_SANITIZE_STRING,
'delete' => FILTER_SANITIZE_STRING,
'deleteEmail' => FILTER_SANITIZE_STRING,
'email' => FILTER_SANITIZE_STRING,
];

$updateData = filter_input_array(INPUT_POST, $arguments);

if (isset($updateData['modify'])) {
    $data = json_decode(base64_decode($updateData['modify']));
    save_position($data);
    exit();
}

if (isset($updateData['reparent'])) {
    $data = json_decode(base64_decode($updateData['reparent']));
    reparent($data);
    exit();
}

if (isset($updateData['delete'])) {
    delete_position($updateData['delete']);
    exit();
}

if (isset($updateData['deleteEmail'])) {
    delete_email($updateData['deleteEmail']);
    exit();
}

if (isset($updateData['email'])) {
    $data = json_decode(base64_decode($updateData['email']));
    save_email($data);
    exit();
}
