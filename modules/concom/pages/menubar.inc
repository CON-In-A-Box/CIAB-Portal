<?php
if (isset($_SESSION['customFields']['currConComPos'])) {
    $base_menus[] = array('base style' => '',
         'selected style' => 'w3-green',
         'function' => 'concom',
         'title' => 'The Current ConCom List',
         'i class' => 'fa fa-phone',
         'text' => 'ConCom List',
         'responsive' => true);
}
