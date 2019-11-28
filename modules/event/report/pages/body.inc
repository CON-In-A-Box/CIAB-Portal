<?php
?>
<script>
    var sidebarMainDiv = 'main_content';
    reportGenerationSidebar.options({
        target: 'event/report'
    });
</script>

<div id="page" class="UI-continer">
  <div id="main_content" class="UI-maincontent">
    <div class='UI-event-sectionbar'>
      <span>Event Reports</span>
    </div>
&nbsp;
    <div class="UI-rest UI-center">
      <div class='VOL-admin'>
        <div id='volunteer_admin_bar' class='VOL-admin-bar'>
          <button id='gen_der_csv' class='UI-eventbutton' onclick='reportGenerationSidebar.open();'>Export CSV Report</button>
        </div>
      </div>
    </div>
  </div>
</div>
