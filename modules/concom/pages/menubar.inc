<?php
if (isset($_SESSION['customFields']['currConComPos']) ||
    isset($_SESSION['IS_ADMIN'])) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'concom',
         'title' => 'The Current ConCom List',
         'i class' => 'fa fa-phone',
         'text' => 'ConCom List',
         'responsive' => true);
}

if (isset($_SESSION['IS_ADMIN'])) {
    $admin_menus[] = array('base style' => 'w3-yellow',
         'selected style' => 'event-color-primary',
         'function' => 'concom/admin',
         'title' => 'ConCom Structure',
         'i class' => 'fa fa-sitemap',
         'text' => 'ConCom Structure',
         'responsive' => true);
}
