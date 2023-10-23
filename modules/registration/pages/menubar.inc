<?php

/*.
    require_module 'standard';
.*/

require_once $BASEDIR.'/console/console.inc';
require_once __DIR__.'/../functions/events.inc';

if (in_registration()) {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'registration',
     'title' => 'Registration',
     'i class' => 'far fa-address-card',
     'text' => 'Member Registration',
     'responsive' => true);

    generate_console_menus('registration');
}


if (!in_console() && RBACApiRequest('registration.reports')) {
    $report_menus[] = array('base style' => 'UI-yellow',
     'selected style' => 'event-color-primary',
     'function' => 'registration/report',
     'title' => 'Registration Reports',
     'i class' => 'far fa-address-card',
     'text' => 'Registration Reports',
     'responsive' => true);
}
