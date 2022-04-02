<?php namespace artshow;

require_once($BACKEND."/RBAC.inc");

if (\ciab\RBAC::havePermission("artshow.admin")) {
    $admin_menus[] = array('base style' => 'UI-yellow',
         'selected style' => 'event-color-primary',
         'function' => 'artshow/admin',
         'title' => 'Art Show Admin',
         'i class' => 'fas fa-paint-brush',
         'text' => 'Art Show Admin',
         'responsive' => true);
}

if (\ciab\RBAC::havePermission("artshow.staff.artist") ||
    \ciab\RBAC::havePermission("artshow.staff.show") ||
    \ciab\RBAC::havePermission("artshow.staff.sales") ||
    \ciab\RBAC::havePermission("artshow.staff.register") ||
    \ciab\RBAC::havePermission("artshow.admin")) {
    $artshow_menus = array();
    $artshow_menus[] = array('base style' => 'UI-main-bar',
         'selected style' => 'event-color-primary',
         'function' => 'artshow',
         'title' => 'Own Art Show Registration',
         'i class' => 'fas fa-paint-brush',
         'text' => 'Own Art Show Registration',
         'responsive' => true);

    if (\ciab\RBAC::havePermission("artshow.staff.artist")) {
        $artshow_menus[] = array('base style' => 'UI-main-bar',
             'selected style' => 'event-color-primary',
             'function' => 'artshow/artist',
             'title' => 'Artist Admin',
             'i class' => 'fas fa-paint-brush',
             'text' => 'Artist Admin (Check-in / Check-out)',
             'responsive' => true);
    }

    if (\ciab\RBAC::havePermission("artshow.staff.sales")) {
        $artshow_menus[] = array('base style' => 'UI-main-bar',
             'selected style' => 'event-color-primary',
             'function' => 'artshow/sales',
             'title' => 'Art Sales',
             'i class' => 'fas fa-paint-brush',
             'text' => 'Art Sales',
             'responsive' => true);
    }

    if (\ciab\RBAC::havePermission("artshow.staff.register")) {
        $artshow_menus[] = array('base style' => 'UI-main-bar',
             'selected style' => 'event-color-primary',
             'function' => 'artshow/checkout',
             'title' => 'Customer Checkout',
             'i class' => 'fas fa-paint-brush',
             'text' => 'Customer Checkout',
             'responsive' => true);
    }

    if (\ciab\RBAC::havePermission("artshow.staff.show")) {
        $artshow_menus[] = array('base style' => 'UI-main-bar',
             'selected style' => 'event-color-primary',
             'function' => 'artshow/showadmin',
             'title' => 'Show Admin',
             'i class' => 'fas fa-paint-brush',
             'text' => 'Show Administration',
             'responsive' => true);
    }


    $base_menus[] = array('base style' => '',
         'base style' => 'UI-button',
         'selected style' => 'event-color-primary',
         'function' => $artshow_menus,
         'title' => 'Art Show',
         'i class' => 'fas fa-paint-brush',
         'text' => 'Art Show',
         'responsive' => true);
} else {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'artshow',
         'title' => 'Art Show',
         'i class' => 'fas fa-paint-brush',
         'text' => 'Art Show',
         'responsive' => true);
}
