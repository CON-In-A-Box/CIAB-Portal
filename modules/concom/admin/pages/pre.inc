<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($BACKEND.'/RBAC.inc');

if (!\ciab\RBAC::havePermission("site.concom.structure")) {
    goSite();
}

require_once(__DIR__."/../../functions/concom.inc");

$arguments = [
'modify' => FILTER_SANITIZE_STRING,
'reparent' => FILTER_SANITIZE_STRING,
'delete' => FILTER_SANITIZE_STRING,
'deleteEmail' => FILTER_SANITIZE_STRING,
'email' => FILTER_SANITIZE_STRING,
'deleteAC' => FILTER_SANITIZE_STRING,
'position' => FILTER_SANITIZE_STRING,
'permission' => FILTER_SANITIZE_STRING,
'addAC' => FILTER_SANITIZE_STRING,
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

if (\ciab\RBAC::havePermission("site.concom.permissions")) {
    if (isset($updateData['deleteAC'])) {
        deleteDepartmentPermission(
            $updateData['deleteAC'],
            $updateData['position'],
            $updateData['permission']
        );
        exit();
    }

    if (isset($updateData['addAC'])) {
        addDepartmentPermission(
            $updateData['addAC'],
            $updateData['position'],
            $updateData['permission']
        );
        exit();
    }

    /* GET */

    $arguments = [
    'permissions' => FILTER_SANITIZE_STRING,
    'fallbackList' => FILTER_SANITIZE_STRING,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['permissions'])) {
        $perm = getDepartmentPermissions($updateData['permissions']);
        print json_encode($perm);
        exit();
    }

    if (isset($updateData['fallbackList'])) {
        $data = buildBackupList($updateData['fallbackList']);
        print json_encode($data);
        exit();
    }
}
