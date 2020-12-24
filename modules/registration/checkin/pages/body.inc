<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
       <div class="UI-event-sectionbar">
            <span>Registration Check-in and Badge Handling</span>
       </div>
       <br>
       <div class="UI-event-sectionbar">
            <span>On-site Registration is Presently:&nbsp;<span id=regopen></span></span>
       </div>
        <br>
        <div class="UI-container UI-padding UI-border UI-center UI-hide UI-bold" id="instructions">
        </div>
        <div class="UI-container UI-padding" id="badges">
            <div class="UI-container UI-padding UI-hide" id="checkin">
               <div class="UI-event-sectionbar">Check-In</div>
            </div>
            <div class="UI-container UI-hide UI-center" id="checkin_button">
                <button class="UI-eventbutton UI-disabled" id="checkinbutton" onclick="checkinPage.doCheckin();">Check In</button>
            </div>
            <div class="UI-container UI-padding UI-hide" id="pickup">
               <div class="UI-event-sectionbar">Boarding Passes for Badge Pickup</div>
            </div>
            <div class="UI-container UI-padding UI-hide" id="lost">
               <div class="UI-event-sectionbar">Report Lost Badges</div>
            </div>
        </div>
    </div>
</div>
