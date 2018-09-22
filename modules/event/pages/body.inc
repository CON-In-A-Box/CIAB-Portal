<?php
require_once __DIR__.'/../functions/functions.inc'
?>

<div id="main_content"  class="w3-container">

    <div id="meetings_content" class="w3-section w3-container">
        <button onclick="expandSection('meetings')" class="2w3-rest w3-center event-color-primary w3-xlarge w3-button w3-block w3-left-align">
            <span>Meetings<span> <i id="meetings_arrow" class="fa fa-caret-up"></i>
        </button>
        <div id="meetings" class="w3-container w3-margin w3-hide w3-show">
            <table class='w3-table w3-striped w3-border w3-white'>
<?php list_meetings() ?>
            </table>
        </div>
    </div>

    <div id="events_content" class="w3-section w3-container">
        <button onclick="expandSection('events')" class="2w3-rest w3-center event-color-primary w3-xlarge w3-button w3-block w3-left-align">
            <span id="events_button">Events<span> <i id="events_arrow" class="fa fa-caret-down"></i>
        </button>
        <div id="events" class="w3-container w3-margin w3-hide">
            <table class="w3-table w3-striped w3-border w3-white w3-padding">
<?php list_events() ?>
            </table>
        </div>
    </div>

    <div id="neon_content" class="w3-section w3-container">
        <button onclick="expandSection('neon')" class="2w3-rest w3-center event-color-primary w3-xlarge w3-button w3-block w3-left-align">
            <span id="neon_button">Neon<span> <i id="neon_arrow" class="fa fa-caret-up"></i>
        </button>
        <div id="neon" class="w3-container w3-margin w3-hide w3-show">
            <div class="w3-container w3-margin w3-center">
                <button id='reload' class='w3-button w3-round-xxlarge w3-red'
                    onclick='reloadFromNeon()'>Reload From Neon</button>
            </div>
        </div>
    </div>

</div>
