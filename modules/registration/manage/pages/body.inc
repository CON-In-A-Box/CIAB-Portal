<script>
    var sidebarMainDiv = 'main_content';
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
       <div class="UI-event-sectionbar">
            <span>Manage Registration</span>
       </div>
       <div class="UI-content UI-padding">
           <div class="UI-event-sectionbar">
                <span>Registered Members</span>
           </div>
           <div class="UI-content" id="members">
           </div>
       </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="modify_ticket">
        <div class="UI-center UI-padding">
            <h2 class="UI-event-sectionbar">Modify Registration</h2>
        </div>

        <div class="UI-content">
            <label class="UI-label" for="badge_name">Badge Name: </label>
            <input class="UI-input UI-padding" type="text" id="badge_name">
            <label class="UI-label" for="contact">Emergency Contact: </label>
            <input class="UI-input UI-padding" type="text" id="contact">
        <div>

        <div class="UI-content UI-center">
            <button class="UI-eventbutton UI-margin" id="save_button">
                Save
            </button>
            <button class="UI-yellowbutton UI-margin"
                onclick="hideSidebar();">
                Close
            </button>
        </div>
    </div>
</div>
