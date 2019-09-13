<?php
/*.
    require_module 'standard';
.*/

if (isset($_SESSION['customFields']['currConComPos']) ||
    isset($_SESSION['IS_ADMIN'])) {
    if (isset($G_CLIENT_SECRET) && isset($G_CLIENT_CRED) &&
        !empty($G_CLIENT_SECRET) && !empty($G_CLIENT_CRED) &&
        isset($G_ROOTFOLDER) && !empty($G_ROOTFOLDER)) {
        $base_menus[] = array('base style' => 'UI-dark-gray',
         'selected style' => 'event-color-primary',
         'function' => 'documents',
         'title' => 'ConCom Documents',
         'i class' => 'fa fa-folder',
         'text' => 'Documents',
         'responsive' => true);
    } elseif (isset($_SESSION['IS_ADMIN'])) {
        $admin_menus[] = array('base style' => 'UI-yellow',
         'selected style' => 'event-color-primary',
         'function' => 'documents',
         'title' => 'Configure Documents',
         'i class' => 'fa fa-folder',
         'text' => 'Configure Documents',
         'responsive' => true);
    }
}
