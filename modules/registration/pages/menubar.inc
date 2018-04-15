<?php

/*.
    require_module 'standard';
.*/

if (class_exists('REGISTRATION') && method_exists('REGISTRATION', 'inRegistration')) {
    $in_registration = REGISTRATION::inRegistration($_SESSION['accountId']);
} else {
    $in_registration = false;
}

if (!empty($_SESSION['IS_ADMIN']) || $in_registration) {
    $base_menus[] = array('base style' => '',
     'selected style' => 'event-color-primary',
     'function' => 'registration',
     'title' => 'Registration',
     'i class' => 'fa fa-address-card-o',
     'text' => 'Member Registration',
     'responsive' => true);
}
