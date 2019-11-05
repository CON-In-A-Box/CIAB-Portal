<?php
if (!empty($_SESSION['customFields']['currConComPos']) ||
    isset($_SESSION['IS_ADMIN'])) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'concom',
         'title' => 'The Current ConCom List',
         'i class' => 'fa fa-phone',
         'text' => 'ConCom List',
         'responsive' => true);
}

if (\ciab\RBAC::havePermission("site.concom.structure")) {
    $admin_menus[] = array('base style' => 'UI-yellow',
         'selected style' => 'event-color-primary',
         'function' => 'concom/admin',
         'title' => 'ConCom Structure',
         'i class' => 'fa fa-sitemap',
         'text' => 'ConCom Structure',
         'responsive' => true);
}
