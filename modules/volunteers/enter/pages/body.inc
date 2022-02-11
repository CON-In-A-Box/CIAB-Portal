<div id="main_content" class="UI-cell-top UI-maincontent">
  <div id="vol-entry">
    <div id="message" v-bind:class="(error)?'VOL-enter-msg-error':'VOL-enter-msg'">{{ message }}</div>
    <form @submit.prevent="submitForm">
      <div v-if="uid" id='userLookup_div' :data-user='uid'></div>
      <div v-else id='userLookup_div'> </div>
      <span v-bind:class="(lookupError)?'VOL-enter-error-span':'VOL-enter-id-span'">{{ lookupMessage }}</span>

      <div class='UI-bar'>
        <div class='UI-bar-item'>
          <label class='UI-label'>Actual hours worked</label>
          <div class='UI-bar'>
            <select class="UI-select UI-bar-item" name="ActualHours" id="hours" @change="calculateHours();" title="Actual Hours Worked." v-model="actualHours">
              <option value=0>0</option>
              <option value=1>1</option>
              <option value=2>2</option>
              <option value=3>3</option>
              <option value=4>4</option>
              <option value=5>5</option>
              <option value=6>6</option>
              <option value=7>7</option>
              <option value=8>8</option>
              <option value=9>9</option>
              <option value=10>10</option>
              <option value=11>11</option>
              <option value=12>12</option>
              <option value=13>13</option>
              <option value=14>14</option>
              <option value=15>15</option>
              <option value=16>16</option>
              <option value=17>17</option>
              <option value=18>18</option>
              <option value=19>19</option>
            </select>
            <select class="UI-select UI-bar-item" name="ActualMinutes" id="minutes" @change="calculateHours();" title="Actual Minutes Worked, to the nearest 15 minutes" v-model="actualMinutes">
              <option value=0 selected>0</option>
              <option value=15>15</option>
              <option value=30>30</option>
              <option value=45>45</option>
            </select>
          </div>
          <input type=hidden name="ActualHoursWorked" id="actualhours" v-model="actualHoursWorked">
        </div>
        <div class='UI-bar-item'>
          <label class='UI-label'>Date/Time that the Shift Ended</label>
          <input class="UI-input" type="date" name="EndDate" id="end_date" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" required @change="checkHours();" v-model="endDate" v-bind:class="(timeInvalid)?'UI-red':''">

          <input class="UI-input UI-bar-item" type=number id="end_hours" style="width: 75px;" required @change="checkHours();" name="EndHours" min=0 v-model="endHour" v-bind:class="(timeInvalid)?'UI-red':''">

          <select class="UI-select UI-bar-item" name="EndMinutes" id="end_minutes" title="" style="height: 40px;" required @change="checkHours();" name="EndMinutes" v-model="endMinutes" v-bind:class="(timeInvalid)?'UI-red':''">
            <option value=0>0</option>
            <option value=15>15</option>
            <option value=30>30</option>
            <option value=45>45</option>
          </select>
          <select class="UI-select UI-bar-item" name="EndAMPM" id="end_ampm" title="" style="height: 40px;" required @change="checkHours();" name="EndAMPM" v-model="endAmPm" v-bind:class="(timeInvalid)?'UI-red':''">
            <option value="AM">AM</option>
            <option value="PM">PM</option>
          </select>
        </div>
      </div>

      <div class='UI-container'>
        <div class='VOL-enter-block'>
          <label class='UI-label'>Volunteer Time Modifier - Must be per policy</label><br />
          <select class="UI-select" style="width:auto" name="TimeModifier" id="modifier" @change="calculateHours();" v-model="timeModifier">
            <option value=1>Normal - 1 hour = 1 hour</option>
            <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
            <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
            <option value=2>Double - 1 hour = 2 hours credit</option>
          </select>
        </div>
        <div class='VOL-enter-block'>
          <label class='UI-label'>Department worked</label><br />
          <select class="UI-select" style="width:auto" name="DepartmentWorked" id="department" v-model="departmentWorked">
            <option v-for="item in departmentsHighlight" :value="departments[item].name">* {{ departments[item].name }}</option>
            <option v-if="departmentsHighlight" disabled>------</option>
            <option v-for="item in Object.keys(departments)" :value="departments[Number(item)].name">
{{ departments[Number(item)].name }}</option>
          </select>
        </div>
      </div>

      <div class='UI-bar'>
        <div class="UI-bar-item UI-center">
          <p name=summery>
            Crediting {{ totalHours }} to {{ volunteerName }}
          </p>
          <p>
            <strong>
              Remember:<br />
              Do Not enter this time on a paper card!
            </strong>
          </p>
        </div>
      </div>

      <div class='UI-bar'>
        <div class="UI-bar-item">
          <div v-if="admin">
            <label class="VOL-authorized-label">Hours Authorized By</label>
            <input class="UI-input" type=text name="AuthorizedBy" v-model="authorizedBy" required>
          </div>
          <div v-else>
            <input type=hidden name="AuthorizedBy" v-model="authorizedBy" required>
          </div>
          <input type=hidden name="EnteredBy" v-model="enteredBy" required>
        </div>
      </div>

      <div class='UI-bar'>
        <div class="UI-bar-item">
          <div class="UI-bar">
            <button id="submit" class="UI-eventbutton VOL-ripple-button" :disabled="uid == null">Submit Hours</button>
            <button class="UI-secondarybutton VOL-ripple-button" type="button" @click="resetForm()">Clear Form</button>
          </div>
        </div>
      </div>

      <div class='UI-bar'>
        <div class="UI-bar-item UI-center" style="width:inherit;">
          <p class="VOL-batch-link">
            <a href='javascript:void(0)' onclick='batch()'>batch file upload</a>
          </p>
        </div>
      </div>
    </form>
  </div>

  <div id='csv_import' class='UI-modal'>
    <div class="UI-modal-content">
      <div class="UI-container">
        <div class="VOL-row">
          <h2 class="UI-center event-color-primary">Volunteer CSV File To Upload</h2>
        </div>
        <div class="VOL-row">
          <p class="UI-margin UI-center">CSV file format:
            <code class="VOL-code-block">&lt;Account,ActualHours,AuthorizedBy,Department,EndDateTime,EnteredBy,Event,TimeModifier&gt;</code></p>
            <p class="UI-margin UI-center"><strong>Account fields</strong> (Account, AuthorizedBy, EnteredBy)  can be badge number, name, or email. However it has to uniquly identify the user so badge number is always the safest.</p>
            <p class="UI-margin UI-center"><strong>Event</strong> is optional. If left empty then the current upcoming event is used.</p>
            <p class="UI-margin UI-center"><strong>EndDateTime</strong> should be quoted. The format is "YYYY-MM-DD HH:MM:SS".</p>
            <h2 class="VOL-enter-block UI-center">Example</h2>
            <table class="UI-stripedtable">
              <tr><td>Attendee Joe,1.000,Connie VonRobot,IT,"2018-06-04 08:15:00",Connie VonRobot,,1.0</tr></td>
              <tr><td>joe@attendee.com,1.000,60055,Gaming,"2018-06-05 08:15:00",Connie VonRobot,,1.0</tr></td>
              <tr><td>60054,1.000,60055,Hotel,"2018-06-06 08:15:00",60055,,1.0</tr></td>
            </table>
          </div>
          <div class="VOL-row">
            <div class="VOL-code-enter">
              <input style="width: 100%;" type="file" id="batch_file" onchange="fileChange()">
            </div>
            <div class="UI-right">
              <button class="UI-redbutton UI-margin" onclick="batchCancel()">Cancel</button>
            </div>
            <div class="UI-right">
              <button class="UI-eventbutton UI-margin UI-disabled" id="batch_import" onclick="batchImport()">Import</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id='csv_import_report' class='UI-modal'>
      <div class="UI-modal-content">
        <div class="UI-container">
          <div>
            <h2 class="UI-center event-color-primary">Volunteer CSV Import Result</h2>
          </div>
          <div>
            <table class='UI-stripedtable' id="import_report">
              <caption>Report from the CSV Import</caption>
              <th scope="col" id="ReportResult">Result</th>
            </table>
          </div>
          <div class="UI-center">
            <button class="UI-eventbutton UI-margin" id="batch_done" onclick="batchDone()">Done</button>
          </div>
        </div>
      </div>
    </div>
  </div><!--close main_content div-->
