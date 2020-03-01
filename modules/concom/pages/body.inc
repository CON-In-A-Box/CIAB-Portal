<script>
  var sidebarMainDiv = 'main_content';
  userLookup.options({
    handler: onLookup
  });
</script>

<div id="page" class="UI-container">
  <div id="main_content" class="UI-maincontent">
    <div class="UI-event-sectionbar">ConCom</div>
    <div id="donut"></div>
    <div id="concom" class="UI-maincontent">

<?php
require_once(__DIR__."/../functions/concom.inc");
?>
    <script>
      var data = <?php print json_encode(buildDonutData());?>;
      drawDonutChart(window.innerWidth-50, window.innerHeight-50, data, '#donut');
    </script>
<?php
ConComListDisplay();
?>
    </div>
  </div>

  <div class='UI-sidebar-hidden UI-fixed' id='add_member_div'>
      <div class='UI-center'>
          <h2 class='UI-red'>Add to Department</h2>
      </div>
      <div class='UI-center'>
          <input class="UI-hiddeninput" id="user_id">
          <label class='UI-label' for='userLookup_div'>
          <h2>Add someone to <span id='add_departemnt'></span></h2></label>
          <fieldset><legend>Search the User Database</legend>
          <div class='UI-rest UI-center' id='userLookup_div'> </div>
          </fieldset>
          <br>
          <label class='UI-label' for='position'>Position:</label>
          <select id="position">
          </select>
      </div>
      <div class='UI-center UI-padding'>
          <button id='add_button' class='UI-eventbutton'
              onclick='onAdd();'>
           OK
          </button>
          <button class='UI-redbutton'
              onclick='hideSidebar();'>
            Close
          </button>
      </div>
  </div>

  <div class='UI-sidebar-hidden UI-fixed' id='edit_member_div'>
      <div class='UI-center'>
          <h2 class='UI-red'>Modify Membership</h2>
      </div>
      <div class='UI-center'>
          <input class="UI-hiddeninput" id="user_id">
          <input class="UI-hiddeninput" id="user_div">
          <input class="UI-hiddeninput" id="user_oldpos">
      </div>
      <div>
        <h3 id='user_name' class='UI-center'></h3>
        <h4 id='user_desc' class='UI-center'></h4>
        <label class='UI-label' for='user_notes'>Note</label>
        <input class="UI-input" id="user_notes" value=''>
        <br>
        <label class='UI-label' for='user_new_pos'>Position:</label>
        <select id="user_pos">
        </select>
      </div>
      <div class='UI-center UI-padding'>
          <button id='add_button' class='UI-eventbutton'
              onclick='updateMember();'>
          Update Member
          </button>
          <button class='UI-redbutton'
              onclick='deleteMember();'>
            Remove
          </button>
          <button class='UI-yellowbutton'
              onclick='hideSidebar();'>
            Close
          </button>
      </div>
  </div>
</div><!--close main_content div-->
