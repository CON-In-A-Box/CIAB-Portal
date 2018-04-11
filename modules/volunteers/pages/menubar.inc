<?php
if (isset($_SESSION['customFields']['currConComPos'])) {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'volunteers/enter',
     'title' => 'Track Volunteer\'s hours',
     'i class' => 'fa fa-clock-o',
     'text' => 'Enter Hours',
     'responsive' => true);
}

if ($_SESSION['IS_VOLUNTEERS'] || !empty($_SESSION['IS_ADMIN'])) {
    $basecolor = 'w3-yellow';
    if (array_key_exists('CIAB_VOLUNTEERADMIN', $_COOKIE) &&
       $_COOKIE["CIAB_VOLUNTEERADMIN"]) {
        $basecolor = 'w3-red';
    }
    $admin_menus[] = array('base style' => $basecolor,
     'selected style' => 'event-color-primary',
     'function' => 'volunteers/admin',
     'title' => 'Volunteer\'s',
     'i class' => 'fa fa-users',
     'text' => 'Volunteers',
     'responsive' => true);
}

if (isset($_SESSION['kioskMode'])) {
    $admin_menus[] = array('base style' => 'w3-right event-secondary-color',
     'selected style' => 'event-color-primary w3-right',
     'function' => 'volunteers/kiosk',
     'title' => 'Change Kiosk User from '.$_SESSION['kioskUser'],
     'i class' => 'fa fa-exchange',
     'text' => 'Change Kiosk User from '.$_SESSION['kioskUser'],
     'responsive' => true);
}
