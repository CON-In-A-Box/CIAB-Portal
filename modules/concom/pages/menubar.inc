<?php
if (isset($_SESSION['customFields']['currConComPos']) ||
    isset($_SESSION['IS_ADMIN'])) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'event-color-primary',
         'function' => 'concom',
         'title' => 'The Current ConCom List',
         'i class' => 'fa fa-phone',
         'text' => 'ConCom List',
         'responsive' => true);
}
