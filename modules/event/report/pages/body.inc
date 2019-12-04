<?php
?>
<script>
    var sidebarMainDiv = 'main_content';
    reportGenerationSidebar.options({
        target: 'event/report',
        reportDisplay: '#report_display'
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
          <button id='gen_der_csv' class='UI-eventbutton' onclick='reportGenerationSidebar.open();'>Generate CSV Report</button>
        </div>
      </div>
       &nbsp;
      <div class='UI-continer UI-padding'>
        <div class='UI-event-sectionbar'>
          <span>Report Data</span>
          </div>
        <div id='report_display'>
        </div>
         &nbsp;
      </div>
      &nbsp;
    </div>
  </div>
</div>
