<?php
$base_menus[] = array('base style' => '',
    'selected style' => 'event-color-primary',
    'function' => 'store/registration',
    'title' => 'The Membership Store',
    'i class' => 'fas fa-phone',
    'text' => 'Membership Store',
    'responsive' => true);

if (\ciab\RBAC::havePermission('site.admin')) {
    $admin_menus[] = array('base style' => 'UI-yellow',
    'selected style' => 'event-color-primary',
    'function' => 'store/admin',
    'title' => 'Manage Stores',
    'i class' => 'fas fa-money-bill',
    'text' => 'Manage Stores',
    'responsive' => true);
}
