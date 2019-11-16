<?php
/*.
    require_module 'standard';
.*/

if (!empty($_SESSION['customFields']['currConComPos']) ||
    isset($_SESSION['IS_ADMIN'])) {
    if (isset($G_CLIENT_SECRET) && isset($G_CLIENT_CRED) &&
        !empty($G_CLIENT_SECRET) && !empty($G_CLIENT_CRED) &&
        isset($G_ROOTFOLDER) && !empty($G_ROOTFOLDER)) {
        $base_menus[] = array('base style' => 'UI-dark-gray',
         'selected style' => 'event-color-primary',
         'function' => 'documents',
         'title' => 'ConCom Documents',
         'i class' => 'fas fa-folder',
         'text' => 'Documents',
         'responsive' => true);
    } elseif (isset($_SESSION['IS_ADMIN'])) {
        $admin_menus[] = array('base style' => 'UI-yellow',
         'selected style' => 'event-color-primary',
         'function' => 'documents',
         'title' => 'Configure Documents',
         'i class' => 'fas fa-folder',
         'text' => 'Configure Documents',
         'responsive' => true);
    }
}
