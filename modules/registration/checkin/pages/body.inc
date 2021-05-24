<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Welcome <?= $_SESSION['preferredName'] ?></span>
        </div>

        <div class="UI-container UI-padding UI-border UI-center UI-hide UI-bold" id="instructions">
        </div>
        <div class="UI-container UI-padding" id="badges">
            <div class="UI-event-sectionbar">Check In for Badge Pickup</div>

            <div class="UI-container UI-padding UI-hide" id="checkin">
            </div>
            <div class="UI-container UI-hide UI-center" id="checkin_button">
                <button class="UI-eventbutton UI-disabled" id="checkinbutton">Check In</button>
            </div>
        </div>
    </div>
</div>
