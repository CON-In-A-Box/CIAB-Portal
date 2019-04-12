<?php
if (isset($_SESSION['IS_ADMIN'])) {
    $admin_menus[] = array('base style' => 'UI-yellow',
     'selected style' => 'event-color-primary',
     'function' => 'admin',
     'title' => 'Oh the administration',
     'i class' => 'fa fa-database',
     'text' => 'Database',
     'responsive' => true);
}
