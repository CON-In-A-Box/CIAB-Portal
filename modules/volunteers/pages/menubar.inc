<?php

require_once($BASEDIR.'/console/console.inc');

if (!empty($_SESSION['customFields']['currConComPos'])) {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'volunteers/enter',
     'title' => 'Track Volunteer\'s hours',
     'i class' => 'far fa-clock',
     'text' => 'Enter Hours',
     'responsive' => true);
}

if (!is_console('volunteers')) {
    if (!empty($_SESSION['customFields']['currConComPos']) ||
        RBACApiRequest('volunteers.admin')) {
        $basecolor = 'UI-yellow';
        if (array_key_exists('CIAB_VOLUNTEERADMIN', $_COOKIE) &&
           $_COOKIE["CIAB_VOLUNTEERADMIN"]) {
            $basecolor = 'UI-red';
        }
        $admin_menus[] = array('base style' => $basecolor,
         'selected style' => 'event-color-primary',
         'function' => 'volunteers/admin',
         'title' => 'Volunteer\'s',
         'i class' => 'fas fa-users',
         'text' => 'Volunteers',
         'responsive' => true);
    }
    if ($_SESSION['IS_VOLUNTEERS'] ||
        RBACApiRequest('volunteers.reports')) {
        $report_menus[] = array('base style' => 'UI-yellow',
         'selected style' => 'event-color-primary',
         'function' => 'volunteers/report',
         'title' => 'Volunteer\'s',
         'i class' => 'fas fa-users',
         'text' => 'Volunteers',
         'responsive' => true);
    }
} else {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'volunteers/admin',
     'title' => 'Volunteer\'s',
     'i class' => 'fas fa-users',
     'text' => 'Volunteers',
     'responsive' => true);
}

generate_console_menus('volunteers');
