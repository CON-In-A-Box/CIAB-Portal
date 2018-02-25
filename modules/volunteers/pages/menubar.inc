<?php
if (isset($_SESSION['customFields']['currConComPos'])) {
    if (isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) {
        $base_menus[] = array('base style' => '',
         'selected style' => 'w3-green',
         'function' => 'volunteers/enter',
         'title' => 'Track Volunteer\'s hours',
         'i class' => 'fa fa-clock-o',
         'text' => 'Enter Hours',
         'responsive' => true);
    }
}

if (isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) {
    $basecolor = 'w3-yellow';
    if (array_key_exists('CIAB_VOLUNTEERADMIN', $_COOKIE) &&
       $_COOKIE["CIAB_VOLUNTEERADMIN"]) {
        $basecolor = 'w3-red';
    }
    $admin_menus[] = array('base style' => $basecolor,
     'selected style' => 'w3-green',
     'function' => 'volunteers/admin',
     'title' => 'Volunteer\'s',
     'i class' => 'fa fa-users',
     'text' => 'Volunteers',
     'responsive' => true);
}
