<?php
?>
<script>
    var sidebarMainDiv = 'main_content';
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
          <button id='gen_der_csv' class='UI-eventbutton' @click='$refs.rpt.open();'>Generate CSV Report</button>
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

  <report-generation ref=rpt target='event/report' report-display='#report_display'>
  </report-generation>

</div>
