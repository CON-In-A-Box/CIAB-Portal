<?php

/*.
    require_module 'standard';
.*/

require_once $BASEDIR.'/kiosk/kiosk.inc';
require_once __DIR__.'/../functions/events.inc';

if (in_registration()) {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'registration',
     'title' => 'Registration',
     'i class' => 'far fa-address-card',
     'text' => 'Member Registration',
     'responsive' => true);

    generate_kiosk_menus('registration');
}
