<?php
/*.
    require_module 'standard';
.*/

PageAllowedByRBAC('site.admin');

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
