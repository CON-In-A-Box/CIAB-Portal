<?php

/*.
    require_module 'standard';
.*/

namespace deadlines\panes;

/* panel */

function deadlines()
{
    ?>
    <div id="deadline_pane"></div>
    <script src="modules/deadlines/sitesupport/deadlinePane.js"></script>
    <?php
    /* done */

}


$homepage_panes[] = 'deadlines\panes\deadlines';
