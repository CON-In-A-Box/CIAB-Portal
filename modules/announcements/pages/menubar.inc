<?php
if (!empty($_SESSION['customFields']['currConComPos']) ||
    RBACApiRequest('announcement.view')) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'announcements',
         'title' => 'Announcements',
         'i class' => 'fas fa-bullhorn',
         'text' => 'Announcements',
         'responsive' => true);
}
