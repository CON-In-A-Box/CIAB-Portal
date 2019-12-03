<div id="page" class="UI-continer">
  <div id="main_content" class="UI-maincontent">
    <div class='UI-event-sectionbar'>
      <span>Web page assets</span>
    </div>
&nbsp;
    <div class="UI-event-sectionbar">
        <span>Configuration</span>
    </div>
    <div class="UI-table" id="config_table">
    </div>
&nbsp;
    <div class="UI-event-sectionbar">
        <span>Assets</span>
    </div>
    <div class="UI-table">
        <div class="UI-table-row UI-padding">
            <div class="UI-table-cell UI-padding" style="font-weight:bold;">
                Asset Name
            </div>
            <div class="UI-table-cell UI-padding" style="font-weight:bold;">
                Current Asset
            </div>
            <div class="UI-table-cell UI-padding" style="font-weight:bold;">
            </div>
        </div>
        <div class="UI-table-row">
            <div class="UI-table-cell UI-padding">
                org-icon
            </div>
            <div class="UI-table-cell UI-padding UI-center">
                <img src='<?php echo \ciab\Asset::load('org-icon');?>'
                 alt=' <Asset>'>
            </div>
            <input type="file" name="assetToUpload.org-icon" id="assetToUpload.org-icon" accept="" style="display:none" onchange="uploadAsset('org-icon', this.files);">
            <div class="UI-table-cell UI-padding">
<button class="UI-eventbutton" onclick="startAssetUpload('org-icon');">Upload Asset</button>
            </div>
        </div>
    </div>
</div>

<script>
if (window.addEventListener) {
  window.addEventListener('load', populateAssetConfiguration);
} else {
  window.attachEvent('onload', populateAssetConfiguration);
}
</script>
