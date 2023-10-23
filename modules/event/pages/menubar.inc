<?php

if (isset($_SESSION['IS_ADMIN'])) {
    $admin_menus[] = array('base style' => 'UI-yellow',
     'selected style' => 'event-color-primary',
     'function' => 'event',
     'title' => 'Event Management',
     'i class' => 'far fa-calendar-alt',
     'text' => 'Events & Meetings',
     'responsive' => true);
}

if (RBACApiRequest('meeting.reports')) {
    $report_menus[] = array('base style' => 'UI-yellow',
     'selected style' => 'event-color-primary',
     'function' => 'event/report',
     'title' => 'Meeting Attendance',
     'i class' => 'far fa-calendar-alt',
     'text' => 'Meeting Attendance',
     'responsive' => true);
}
