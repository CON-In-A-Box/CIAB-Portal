<script>
    userProfile.options({
        title: 'Artist Information',
        updateButtonText: '',
        inlineUpdateButton: false,
        panes: ['name', 'badge', 'emailPrimary', 'phone', 'addr'],
        onChange: artistPage.artistDataChange
    });
    userLookup.options({
        message: "",
        partialMatch: true,
        needForm: false,
        handler: artistPage.lookup
        });
</script>
<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div id="left_side" class="ARTSHOW-left-bar">

        <div class='UI-bar-item' id='userLookup_div'>
        </div>

<select class="ARTSHOW-artist-list"  id="artist_list" size=50 onchange="artistPage.selectArtist();">
</select>
        <div class="UI-center">
            <button class="UI-eventbutton UI-margin" id="new_artist_button"
                onclick="artistPage.newArtist(true);">New Artist</button>
            <button class="UI-graybutton UI-margin" id="save_artist_button"
                onclick="artistPage.saveArtist();" disabled>Save</button>
        </div>
        </div>

        <div id="right_side" class="ARTSHOW-main-page">
        <div class="UI-event-sectionbar">
            <span>Art Show Artists</span>
        </div>
        <div id="headline_section" class="UI-container UI-border UI-margin UI-padding UI-hide">
        </div>
        <div>

    <div class="UI-container UI-center">
        <button class="UI-eventbutton UI-margin" id="artist_art"
            onclick="artistPage.artistArt();" disabled>Artist's Art</button>
    </div>

    <div id="userProfile">
    </div>

<p></p>
  <div class="ARTSHOW-profile-company-div">
    <div>
      Company Name
    </div>
    <div class="UI-container">
        <div class="ARTSHOW-profile-white-div">
            <input class="UI-input" id="company" value='' placeholder="<none>" onchange="artistPage.artistDataChange();">
            <div class="UI-container UI-border UI-padding" id="artist_company">
                <input type="checkbox" class="UI-checkbox" id="use_company" name="use_company" onchange="artistPage.artistDataChange();">
                <label class="UI-label UI-padding" for="use_company">Use company name on bid tags.</label>
                <br>
                <input type="checkbox" class="UI-checkbox" id="use_company_check" name="use_company_check" onchange="artistPage.artistDataChange();">
                <label class="UI-label UI-padding" for="use_company_check">Make payments to company name.</label>
            </div>
        </div>
    </div>
</div>

<p></p>
<div class="ARTSHOW-profile-website-div">
    <div>
      WebSite
    </div>
    <div class="UI-container">
            <input class="UI-input" id="website" value='' placeholder="<none>" onchange="artistPage.artistDataChange();">
    </div>
</div>
<p></p>
<div class="ARTSHOW-profile-status-div">
    <div>
       Status
    </div>
    <div class="UI-container">
        <div class="ARTSHOW-profile-white-div">
            <input type="radio" class="UI-radio" id="professional" name="status"
             value="professional" onchange="artistPage.artistDataChange();">
            <label for="professional">Professional</label>
            <input type="radio" class="UI-radio" id="amateur" name="status"
             value="amateur" checked onchange="artistPage.artistDataChange();">
            <label for="amateur">Amateur</label>
        </div>
    </div>
</div>
<p></p>
<div class="ARTSHOW-mail-div">
    <div>
        Mail In Art
    </div>
    <div class="UI-container">
      <div class="ARTSHOW-profile-white-div">
        <div class="UI-container UI-padding">
            <input type="checkbox" class="UI-checkbox UI-padding" id="reg_mailin" name="reg_mailin" onchange="artistPage.artistDataChange();">
            <label class="UI-label UI-padding" for="reg_mailin">Artist will be mailing art.</label>
        </div>
        <div class="UI-container">
        <label for="reg-return-type" class="UI-label UI-quarter">Return artwork via:</label>
        <select name="reg-return-type" id="reg-return-type" class="UI-input UI-threequarter" onchange="artistPage.artistDataChange();">
        </select>
        </div>
      </div>
    </div>
</div>
<input class="UI-hiddeninput" id="event_details_present" value="0" >
<p></p>
<div class="ARTSHOW-questions-div">
    <div>
        Artist Questions
    </div>
    <div class="UI-container">
      <div class="ARTSHOW-profile-white-div">
        <div id="artist_event_questions">
        </div>
      </div>
    </div>
</div>
