<?php


function _htmlsafeify($array)
{
    $output = [];
    foreach ($array as $key => $value) {
        $output[$key] = htmlspecialchars($value);
    }
    return $output;

}


function hour_table()
{
    $concom = null;
    $data = get_volunteer_hour_summary();
    $summary = get_volunteer_year_summery();
    if (!empty($data)) {
        $keys = array_keys($data[0]);
        $id_index = array_search("Department ID", $keys);
        if ($id_index !== false) {
            unset($keys[$id_index]);
        }
        foreach ($data as $key => $record) {
            echo "<tr class='VOL-hover-red' ";
            echo 'onclick=\'departmentReport("'.$record['Department Worked'].'", '.$record['Department ID'].');\'';
            echo ">";
            echo "<td>";
            unset($record['EntryID']);
            if ($id_index !== false) {
                unset($record['Department ID']);
            }
            if (!isset($uid)) {
                $record['Total Hours'] += 0;
            } else {
                $record['Actual Hours'] += 0;
                $record['Time Modifier'] += 0;
            }
            echo implode(_htmlsafeify($record), '</td><td>');
            echo "</td></tr>\n";
        }
    } else {
        echo "<tr><td class='VOL-no-entries' colspan=9>No Entries...</td></tr>\n";
    }

};


?>
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
          <button id='min_hours' class='UI-eventbutton' onclick='minHourReport();'>Minimum Hour Report</button>
          <button id='gen_der_csv' class='UI-eventbutton' onclick='generateDerivedCSV();'>Export Derived CSV</button>
          <button id='gen_csv' class='UI-eventbutton' onclick='generateCSV();'>Export Raw CSV Table</button>
        </div>
      </div>
    </div>

    <div class="UI-container UI-center event-color-primary">
      <div class='UI-tabletitle'>Hours</div>
      <table class='UI-stripedtable' summary='Department Volunteer Summery'>
        <tr>
            <th id="Department">Department Worked</th>
            <th id="Entries">Total Entries</th>
            <th id="Volunteers">Total Volunteers</th>
            <th id="Hours">Total Hours</th>
        </tr>
<?php hour_table();?>
      </table>
      <div class='UI-tablefooter'>&nbsp;</div>
    </div>
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

  <div class='UI-sidebar-hidden UI-fixed' id='csv_export_div'>
    <div class='UI-center'>
      <h2 class='UI-red'>Export Raw CSV Data</h2>
    </div>
    <div class='UI-center'>
      <label class='UI-label' for='csv_table'>
      Table:</label>
      <select class="UI-margin UI-select" style="width:auto" id="csv_table">
        <option value=HourRedemptions>HourRedemptions</option>
        <option value=VolunteerHours selected>VolunteerHours</option>
        <option value=VolunteerRewards>VolunteerRewards</option>
        <option value=RewardGroup>RewardGroup</option>
      </select>
    </div>
    <div class='UI-center'>
      <button id='generate_csv' class='UI-eventbutton'
          onclick='generatCSVReport();'>
         Generate CSV
      </button>
      <button class='UI-redbutton'
          onclick='hideSidebar();'>
        Close
      </button>
    </div>
  </div>

  <div class='UI-sidebar-hidden UI-fixed' id='csv_export_derived_div'>
    <div class='UI-center'>
      <h2 class='UI-red'>Export Derived CSV Data</h2>
    </div>
    <div class='UI-center'>
      <label class='UI-label' for='der_csv_table'>
      Table:</label>
      <select class="UI-margin UI-select" style="width:auto" id="der_csv_table">
        <option>Volunteer Rewards</option>
        <option>Volunteer Hours</option>
        <option>Prize Redemption</option>
      </select>
    </div>
    <div class='UI-center'>
      <button id='generate_csv' class='UI-eventbutton'
          onclick='generateDerivedCSVReport();'>
         Generate CSV
      </button>
      <button class='UI-redbutton'
          onclick='hideSidebar();'>
        Close
      </button>
    </div>
  </div>

  <div class='UI-sidebar-hidden UI-fixed' id='hour_report_div'>
    <div class='UI-center'>
      <h2 class='UI-red'>Report of all Volunteers with minimum hours or more</h2>
    </div>
    <div class='UI-center'>
      <label class='UI-label' for='report_hour_min'>
      Minimum hours:</label>
      <input class="UI-input" id="report_hour_min">
    </div>
    <div class='UI-center'>
      <button id='generate_report' class='UI-eventbutton'
          onclick='generateHourReport();'>
         Generate Report &lt;CSV&gt;
      </button>
      <button class='UI-redbutton'
          onclick='hideSidebar();'>
        Close
      </button>
    </div>
  </div>
</div>
