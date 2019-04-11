<?php
require_once __DIR__.'/../functions/functions.inc'
?>

<script>
    var sidebarMainDiv = 'main_content';
</script>

<div id="page"  class="UI-container">
    <div id="main_content"  class="UI-maincontent">

        <div id="meetings_content" class="w3-section UI-container">
            <button onclick="expandSection('meetings')" class="UI-event-sectionbar UI-button w3-block w3-left-align">
                <span>Meetings<span> <i id="meetings_arrow" class="fa fa-caret-up"></i>
            </button>
            <div id="meetings" class="UI-container UI-margin w3-hide w3-show">
                <table class='UI-stripedtable'>
    <?php list_meetings() ?>
                </table>
                <div class="UI-container UI-margin UI-center">
                    <button id='new' class='UI-eventbutton'
                        onclick='newMeeting()'>Add New Meeting</button>
                </div>
            </div>
        </div>

        <div id="cycles_content" class="w3-section UI-container">
            <button onclick="expandSection('cycles')" class="UI-event-sectionbar UI-button w3-block w3-left-align">
                <span id="cycles_button">Annual Cycles<span> <i id="cycles_arrow" class="fa fa-caret-down"></i>
            </button>
            <div id="cycles" class="UI-container UI-margin w3-hide  w3-responsive">
                <div class="UI-container UI-margin UI-center">
                    <button id='new_cycle' class='UI-eventbutton'
                        onclick='newCycle()'>Add New Cycle</button>
                </div>
                <table class="UI-stripedtable w3-padding">
    <?php list_cycles() ?>
                </table>
            </div>
        </div>

        <div id="events_content" class="w3-section UI-container">
            <button onclick="expandSection('events')" class="UI-event-sectionbar UI-button w3-block w3-left-align">
                <span id="events_button">Events<span> <i id="events_arrow" class="fa fa-caret-down"></i>
            </button>
            <div id="events" class="UI-container UI-margin w3-hide">
             <div class="UI-container UI-margin UI-center">
                <button id='new_event' class='UI-eventbutton'
                    onclick='newEvent()'>Add New Event</button>
             </div>
             <div>
                <table class="UI-stripedtable w3-padding">
    <?php list_events() ?>
                </table>
             </div>
            </div>
        </div>

        <div id="neon_content" class="w3-section UI-container">
            <button onclick="expandSection('neon')" class="UI-event-sectionbar UI-button w3-block w3-left-align">
                <span id="neon_button">Neon<span> <i id="neon_arrow" class="fa fa-caret-up"></i>
            </button>
            <div id="neon" class="UI-container UI-margin w3-hide w3-show">
                <div class="UI-container UI-margin UI-center">
                    <button id='reload' class='UI-redbutton'
                        onclick='reloadFromNeon()'>Reload From Neon</button>
                </div>
            </div>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='edit_meeting' style="display: inline-block; position: fixed;">
        <div class='UI-center'>
            <h2 class='UI-red'>Meeting Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="meet_id" value='-1' readonly>
            <label for='meet_name'>Meeting Name:</label>
            <input class="UI-input" id="meet_name" value='New Meeting'>
            <label for='meet_event'>Event:</label>
            <select class="UI-input" id="meet_event">
<?php
$events = select_events();
foreach ($events as $event) {
    print "<option value=".$event['Id'].">".$event['Name']."</option>\n";
}
?>
            </select>
            <label for='meet_date'>Date:</label>
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

    <div class='w3-hide w3-padding' id='edit_cycle' style="display: inline-block; position: fixed;">
        <div class='UI-center'>
            <h2 class='UI-red'>New Cycle Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="cycle_id" value='-1' readonly>
            <label for='cycle_from'>From:</label>
            <input class="UI-input" type="date" id="cycle_from"
                value="<?php echo date('Y-m-d', strtotime($last_cycle['To']." +1 days"));?>">
            <label for='cycle_to'>To:</label>
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

    <div class='w3-hide w3-padding' id='edit_badge' style="display: inline-block; position: fixed;">
        <div class='UI-center'>
            <h2 class='UI-red'>Badge Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="badge_id" value='-1' readonly>
            <input class="UI-hiddeninput" id="badge_event" value='-1' readonly>
            <label for='badge_name'>Badge Name:</label>
            <input class="UI-input" id="badge_name" value='New Badge'>
            <label for='badge_event_name'>Event:</label>
            <input class="UI-input" id="badge_event_name" value='' readonly>
            <label for='badge_cost'>Badge Cost:</label>
            <input class="UI-input" id="badge_cost" type=int value=0>
            <label for='badge_from'>Avaliable From:</label>
            <input class="UI-input" type="date" id="badge_from">
            <label for='badge_to'>Avalible To:</label>
            <input class="UI-input" type="date" id="badge_to">
            <label for='badge_image'>Background Image File on Printer:</label>
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

    <div class='w3-hide w3-padding' id='edit_event' style="display: inline-block; position: fixed;">
        <div class='UI-center'>
            <h2 class='UI-red'>Event Details</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="event_id" value='-1' readonly>
            <label for='event_name'>Event Name:</label>
            <input class="UI-input" id="event_name" value='New Event'>
            <label for='event_from'>From:</label>
            <input class="UI-input" type="date" id="event_from">
            <label for='event_to'>To:</label>
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

    <div class='w3-hide w3-padding' id='import_concom' style="display: inline-block; position: fixed;">
        <div class='UI-center'>
            <h2 class='UI-red'>Import Concom</h2>
        </div>
        <div>
            <input class="UI-hiddeninput" id="concom_event_id" value='-1' readonly>
            <label for='event_from'>From:</label>
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
