<?php
if (isset($_SESSION['IS_ADMIN'])) {
    $admin_menus[] = array('base style' => 'w3-yellow',
     'selected style' => 'event-color-primary',
     'function' => 'event',
     'title' => 'Event Management',
     'i class' => 'fa fa-calendar',
     'text' => 'Events & Meetings',
     'responsive' => true);
}
