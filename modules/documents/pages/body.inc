<div id="page" class="UI-container">
<div id="main_content" class="UI-maincontent">
<h2 class="UI-event-sectionbar"> Staff Shared Documents </h2>
<?php

/*.
    require_module 'standard';
.*/

require_once __DIR__.'/../functions/documents.inc';

$client_info = create_client();
$root = '/';

if ($client_info[0] == null) {
?>
    <h3 class="UI-center">Service Not Configured</h3>

    <div>
    <span>You need to setup a google API key for the account that is
going to be accessed. Setup an OAuth 'Client ID' of type <b>other</b>.  <a href="https://console.developers.google.com/apis/credentials">HERE</a>.</span>
    <br>
    <span> Once you have done that you need to download the JSON for the key. It will be saved as "client_secret_&lt;something&gt;.json. Open the text file and copy the contents here: </span>
<div><textarea class="UI-input" id="secret"></textarea>
<button id="set_code" class="UI-eventbutton" onclick='setSecret()'>Enter</button>
    </div>
<?php
exit();
}

if ($client_info[1] === false) {
    $url = get_auth_url($client_info[0]);
?>
<hr>
<h3 class="UI-center">Service needs authorization</h3>
<div><span>Click <a href="<?php echo $url;?>" target="_blank">HERE</a> to get authorization code</span></div>
<div><label>Enter Code Here:</label><input class="UI-input" id="code">
<button id="set_code" class="UI-eventbutton" onclick='authCode()'>Enter</button>
</div>
<?php
exit();
}

if (!isset($G_ROOTFOLDER) || $G_ROOTFOLDER === "") {
?>
<hr>
<h3 class="UI-center">Configure root folder</h3>
<div><label>Shared Drive Root Folder:</label><input class="UI-input" id="folder">
<button id="set_folder" class="UI-eventbutton" onclick='setFolder()'>Enter</button>
</div>
<?php
exit();
} else {
/*. string .*/
    if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
        $arguments = [
        'path'      => FILTER_SANITIZE_SPECIAL_CHARS,
        ];
        $updateData = filter_input_array(INPUT_GET, $arguments);
        $root = $updateData['path'];
    }
    echo '<h2 class="UI-center"><b id="root_path">'.$root.'</b></h2>';

?>
<table class="UI-stripedtable UI-padding" id="folder_table">
<tr><th class="event-color-primary UI-center">Folder</th></tr>
</table>
<br>
<table class="UI-stripedtable UI-padding" id="file_table">
<tr><th class="event-color-primary UI-center">File</th><th class="event-color-primary UI-center">Last Modified</th></tr>
<?php
}
?>
</table>
<script>
<?php
echo 'loadFiles("'.$root.'");';
?>
</script>
</div>
</div>
