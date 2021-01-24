<?php
/*.
    require_module 'standard';
.*/

require_once($BACKEND."/RBAC.inc");

if (!\ciab\RBAC::havePermission("site.admin")) {
    goSite();
}

if (!empty($_REQUEST)) {
    $arguments = [
    'disabledModules'   => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['disabledModules'])) {
        set_conf_value('DISABLEDMODULES', $updateData['disabledModules']);
        goSite();
    }
}
