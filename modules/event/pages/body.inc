<?php
require_once __DIR__.'/../functions/functions.inc'
?>

<script>
    var sidebarMainDiv = 'main_content';
</script>

<div id="page"  class="UI-container">
    <div id="main_content"  class="UI-maincontent">

        <div id="meetings_content" class="EVENT-content">
            <button onclick="expandSection('meetings')" class="EVENT-section-bar">
                <span>Meetings<span> <em id="meetings_arrow" class="fas fa-caret-up"></em>
            </button>
            <div id="meetings" class="UI-container UI-margin UI-hide UI-show">
<?php list_meetings() ?>
                <div class="UI-container UI-margin UI-center">
                    <button id='new' class='UI-eventbutton'
                        onclick='newMeeting()'>Add New Meeting</button>
                </div>
            </div>
        </div>

        <div id="cycles_content" class="EVENT-content">
            <button onclick="expandSection('cycles')" class="EVENT-section-bar">
                <span id="cycles_button">Annual Cycles<span> <em id="cycles_arrow" class="fas fa-caret-down"></em>
            </button>
            <div id="cycles" class="UI-container UI-margin UI-hide">
                <div class="UI-container UI-margin UI-center">
                    <button id='new_cycle' class='UI-eventbutton'
                        onclick='newCycle()'>Add New Cycle</button>
                </div>

                <div id="cycle_list" class="UI-table-all">
                  <div class="UI-table-header">
                    <div class="UI-table-row">
                      <div class="UI-table-cell">
                            Id
                      </div>
                      <div class="UI-table-cell">
                            From
                      </div>
                      <div class="UI-table-cell">
                            To
                      </div>
                      <div class="UI-table-cell">
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <div id="events_content" class="EVENT-content">
            <button onclick="expandSection('events')" class="EVENT-section-bar">
                <span id="events_button">Events<span> <em id="events_arrow" class="fas fa-caret-down"></em>
            </button>
            <div id="events" class="UI-container UI-margin UI-hide">
             <div class="UI-container UI-margin UI-center">
                <button id='new_event' class='UI-eventbutton'
                    onclick='newEvent()'>Add New Event</button>
             </div>
             <div>
<?php list_events() ?>
             </div>
            </div>
        </div>
<?php
if (array_key_exists('Neon', $GLOBALS)) {
?>
        <div id="neon_content" class="EVENT-content">
            <button onclick="expandSection('neon')" class="EVENT-section-bar">
                <span id="neon_button">Neon<span> <em id="neon_arrow" class="fas fa-caret-up"></em>
            </button>
            <div id="neon" class="UI-container UI-margin UI-hide UI-show">
                <div class="UI-container UI-margin UI-center">
                    <button id='reload' class='UI-redbutton'
                        onclick='reloadFromNeon()'>Reload From Neon</button>
                </div>
            </div>
        </div>
<?php
}
?>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='edit_meeting'>
        <div class='UI-center'>
            <h2 class='UI-red'>Meeting Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="meet_id" value='-1' readonly>
            <label class='UI-label' for='meet_name'>Meeting Name:</label>
            <input class="UI-input" id="meet_name" value='New Meeting'>
            <label class='UI-label' for='meet_event'>Event:</label>
            <select class="UI-input" id="meet_event">
<?php
$events = select_events();
foreach ($events as $event) {
    print "<option value=".$event['Id'].">".$event['Name']."</option>\n";
}
?>
            </select>
            <label class='UI-label' for='meet_date'>Date:</label>
            <input class="UI-input" type="date" id="meet_date"
                value="<?php echo date('Y-m-d');?>">
        </div>
        <hr>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='saveMeeting();'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='edit_cycle'>
        <div class='UI-center'>
            <h2 class='UI-red'>Cycle Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="cycle_id" value='-1' readonly>
            <label class='UI-label' for='cycle_from'>From:</label>
            <input class="UI-input" type="date" id="cycle_from"
                value="<?php echo date('Y-m-d', strtotime($last_cycle['To']." +1 days"));?>">
            <label class='UI-label' for='cycle_to'>To:</label>
            <input class="UI-input" type="date" id="cycle_to"
                value="<?php echo date('Y-m-d', strtotime($last_cycle['To']." +1 years"));?>">
        </div>
        <hr>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='saveCycle();'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='edit_badge'>
        <div class='UI-center'>
            <h2 class='UI-red'>Badge Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="badge_id" value='-1' readonly>
            <input class="UI-hiddeninput" id="badge_event" value='-1' readonly>
            <label class='UI-label' for='badge_name'>Badge Name:</label>
            <input class="UI-input" id="badge_name" value='New Badge'>
            <label class='UI-label' for='badge_event_name'>Event:</label>
            <input class="UI-input" id="badge_event_name" value='' readonly>
            <label class='UI-label' for='badge_cost'>Badge Cost:</label>
            <input class="UI-input" id="badge_cost" type=int value=0>
            <label class='UI-label' for='badge_from'>Avaliable From:</label>
            <input class="UI-input" type="date" id="badge_from">
            <label class='UI-label' for='badge_to'>Avalible To:</label>
            <input class="UI-input" type="date" id="badge_to">
            <label class='UI-label' for='badge_image'>Background Image File on Printer:</label>
            <input class="UI-input" id="badge_image" value=''>
        </div>
        <hr>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='saveBadge();'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='edit_event'>
        <div class='UI-center'>
            <h2 class='UI-red'>Event Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="event_id" value='-1' readonly>
            <label class='UI-label' for='event_name'>Event Name:</label>
            <input class="UI-input" id="event_name" value='New Event'>
            <label class='UI-label' for='event_from'>From:</label>
            <input class="UI-input" type="date" id="event_from">
            <label class='UI-label' for='event_to'>To:</label>
            <input class="UI-input" type="date" id="event_to">
        </div>
        <hr>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='saveEvent();'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='UI-sidebar-hidden UI-fixed' id='import_concom'>
        <div class='UI-center'>
            <h2 class='UI-red'>Import Concom</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="concom_event_id" value='-1' readonly>
            <label class='UI-label' for='event_from'>From:</label>
            <select class="UI-input" id="import_from">
<?php
$events = select_events();
foreach ($events as $event) {
    print "<option value=".$event['Id'].">".$event['Name']."</option>\n";
}
?>
            </select>
        </div>
        <hr>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='doImport();'>
              Import
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

</div>
