<?php
if (!empty($_SESSION['customFields']['currConComPos']) ||
    RBACApiRequest('deadline.view')) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'deadlines',
         'title' => 'Deadlines',
         'i class' => 'fas fa-tasks',
         'text' => 'Deadlines',
         'responsive' => true);
}
