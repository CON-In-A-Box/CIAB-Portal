<?php
if (\ciab\RBAC::havePermission("announcement.view") ||
    !empty($_SESSION['customFields']['currConComPos'])) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'announcements',
         'title' => 'Announcements',
         'i class' => 'fas fa-bullhorn',
         'text' => 'Announcements',
         'responsive' => true);
}
