<?php

/*.
    require_module 'standard';
.*/

namespace announcements\panes;

/* panel */

function announcements()
{
?>
    <div id="announcement_pane" class="UI-hide"></div>
    <script src="modules/announcements/sitesupport/announcementPane.js"></script>
<?php
    /* done */

}


$homepage_panes[] = 'announcements\panes\announcements';
