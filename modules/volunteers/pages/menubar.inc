<?php

require_once $BASEDIR.'/kiosk/kiosk.inc';

if (isset($_SESSION['customFields']['currConComPos'])) {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'volunteers/enter',
     'title' => 'Track Volunteer\'s hours',
     'i class' => 'fa fa-clock-o',
     'text' => 'Enter Hours',
     'responsive' => true);
}

if (!is_kiosk('volunteers')) {
    if ($_SESSION['IS_VOLUNTEERS'] || !empty($_SESSION['IS_ADMIN']) ||
        isset($_SESSION['customFields']['currConComPos'])) {
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
} else {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'volunteers/admin',
     'title' => 'Volunteer\'s',
     'i class' => 'fa fa-users',
     'text' => 'Volunteers',
     'responsive' => true);
}

generate_kiosk_menus('volunteers');
