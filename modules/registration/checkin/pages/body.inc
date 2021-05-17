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
        <!-- TODO: These are temporary and not-smart links to the other pages -->
        <div><a href="/index.php?Function=registration/boarding">Display Boarding Passes</a></div>
        <div><a href="/index.php?Function=registration/lost">Report Lost Badge(s)</a></div>
    </div>
</div>
