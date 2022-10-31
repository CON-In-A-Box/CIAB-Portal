<script>
    var sidebarMainDiv = 'main_content';
</script>

<div id="page" class="UI-container">
    <div id="main_content"  class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Deadlines</span>
            <button class="UI-button UI-right UI-hide" id="deadline-sectionbar-add">
                <em class="fas fa-plus-square"></em>
            </button>
        </div>
        <div id="headline_section" class="UI-container">
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="modify_deadline">
        <div class="UI-center">
            <h2 class="UI-red"><span id="modify_title">Modify</span> Deadline</h2>
        </div>
        <hr>
        <div class="UI-padding">
            <div id="dept_drop"></div>
            &nbsp;
            <input class="UI-input UI-padding" type="date" id="deadline_date">
            &nbsp;
            <label class="UI-label" for="deadline_note">Note: </label>
            <input class="UI-input UI-padding" type="text" id="deadline_note">
            &nbsp;
            <div class="UI-padding">
                <label class="UI-label" for="scope_drop">Audience: </label>
                <select id="scope_drop">
                    <option value=2>This Department Only</option>
                    <option value=1>All ConCom</option>
                    <option value=0>Convention Wide (All Attendees)</option>
                </select>
            </div>
        </div>
        <div class="UI-center">
            <button class="UI-eventbutton UI-margin UI-hide" id="save_event_button"
                onclick="deadlinePage.updateEvent(this);">
                Save
            </button>
            <button class="UI-redbutton UI-margin UI-hide" id="remove_event_button"
                onclick="deadlinePage.removeEvent(this);">
                Remove
            </button>
            <button class="UI-yellowbutton UI-margin"
                onclick="hideSidebar();">
                Close
            </button>
        </div>
    </div>

</div>
