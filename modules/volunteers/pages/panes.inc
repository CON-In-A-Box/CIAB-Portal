<?php

/*.
    require_module 'standard';
.*/

namespace volunteers\panes;

/* panel */

function display_volunteer_pane()
{
    ?>
    &nbsp;
    <script type="module" src="modules/volunteers/vue/volunteerPane.js"></script>
    <div id="vol-div">
        <vol-pane :open="open" :entries="entries" :total-hours="totalHours" :items="items" :total-spent="totalSpent">
        </vol-pane>
    </div>
    <?php
    /* done */

}


$homepage_panes[] = 'volunteers\panes\display_volunteer_pane';
