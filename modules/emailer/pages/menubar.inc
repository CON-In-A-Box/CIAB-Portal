<?php namespace mailer;

require_once __DIR__."/../functions/emailer.inc";

if (email::allowed()) {
    $admin_menus[] = array('base style' => 'w3-yellow',
        'selected style' => 'event-color-primary',
        'function' => 'emailer',
        'title' => 'Send E-mail',
        'i class' => 'fa fa-envelope',
        'text' => 'Send E-mail',
        'responsive' => true);
}
