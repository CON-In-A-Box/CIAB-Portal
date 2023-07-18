<script>
    var sidebarMainDiv = 'main_content';
</script>

<div id="page" class="UI-continer">

  <div id="main_content" class="UI-maincontent">
    <div class='UI-event-sectionbar'>
      <span>Volunteer Reports</span>
    </div>

    <div class="UI-rest UI-center">
      <div class='VOL-admin'>
        <div id='volunteer_admin_bar' class='VOL-admin-bar'>
          <button id='min_hours' class='UI-eventbutton' @click='$refs.mhr.open();'>Minimum Hour Report</button>
          <button id='gen_der_csv' class='UI-eventbutton' @click='$refs.rpt.open();'>Export CSV Report</button>
        </div>
      </div>
    </div>

    <volunteer-hour-table></volunteer-hour-table>

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

  <div class='UI-sidebar-hidden UI-fixed' id='department_report_div'>
    <div class='UI-center'>
      <h2 class='UI-red'>Department Report</h2>
    </div>
    <hr>
    <div>
      <h2 id='dept_name' class='event-color-secondary'></h2>
      <input class="UI-hiddeninput" id="dept_data" readonly>
      <input class="UI-hiddeninput" id="dept_data_name" readonly>
    </div>
    <hr>
    <div class='UI-center'>
      <button id='generate_dept_report' class='UI-eventbutton'
          onclick='generateDeptReport();'>
         Generate Report &lt;CSV&gt;
      </button>
      <button class='UI-redbutton'
          onclick='hideSidebar();'>
        Close
      </button>
    </div>
  </div>

  <volunteer-report ref=mhr report-display="#report_display" have-event header="Report of all Volunteers with minimum hours or more">
  </volunteer-report>

  <report-generation ref=rpt target='volunteers/report' report-display='#report_display'>
  </report-generation>
</div>
