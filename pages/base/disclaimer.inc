<?php
$year = date('Y');
?>
<p></p>
<div class="UI-bar">
  <div class="UI-dropdown-hover">
    <button class="UI-disclaimer-info-button"><img src="images/dualXicon.png" style="height: 2em; width: 2em;" alt="Dual X Icon - Con-In-A-Box"> Information</button>
    <div class="UI-dropdown-content">
      <div class="UI-disclaimer-info-panel">
        <div class="UI-disclaimer-copyright-div">
          Con-In-A-Box&trade;
<?php
echo 'In use by '.$CONHOST;
?>
          <hr />
        </div>
<?php
echo '<div class="UI-modules-panel">';
echo 'Included Modules:';
echo '<ul>';
echo '<li>'.$MODULENAME.' v-'.getVersion()."</li>";
echo '</ul>';
echo "</div>\n";
?>
      </div>

      <div class="UI-small-disclaimer-panel UI-lime">
        All parts of Con-In-A-Box&trade;, including but not limited to the Dual-X icon, CON-In-A-Box&trade; logo, name CON-In-A-Box&trade;, scripts, programs, coding, styles, data models, CGI, HTML, custom PHP, structure, feeds, or methods, except where otherwise protected (exceptions include but not limited to the Neon-php library maintained by z2systems) are:
        <div class="UI-large-center-container">
          &copy; Copyright 2005-<?php echo $year; ?> - Convergence Events, Inc.
        </div>
      </div>

      <div class="UI-small-disclaimer-panel UI-khaki">
        Licensed under the Apache License, Version 2.0 (the "License"); you may not use any part of Con-In-A-Box&trade; except in compliance with the License. You may obtain a copy of the License at

        <div class="UI-container">
          <a href="http://www.apache.org/licenses/LICENSE-2.0">http://www.apache.org/licenses/LICENSE-2.0</a>
        </div>

        Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
      </div>

      <div class="UI-disclaimer-panel UI-orange">
        Actual data information, entries, and tracking are Private and Confidential and may not be used in anyway the is contrary to the policies of the group, legal entity, or organization using Con-In-A-Box&trade;.  By accessing this site, you agree and consent to same said policies and acknowledge any legal responsibility therein contained.
      </div>
    </div>
  </div>
</div>
