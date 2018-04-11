<div class="w3-content">
<h2 class="w3-center w3-green"> Staff Shared Documents </h2>
<?php

require_once __DIR__.'/../functions/documents.inc';

$client_info = create_client();

if ($client_info[0] == null) {
?>
    <h3 class="w3-center">Service Not Configured</h3>

    <div>
    <span>You need to setup a google API key for the account that is
going to be accessed. Setup an OAuth 'Client ID' of type <b>other</b>.  <a href="https://console.developers.google.com/apis/credentials">HERE</a>.</span>
    <br>
    <span> Once you have done that you need to download the JSON for the key. It will be saved as "client_secret_&lt;something&gt;.json. Open the text file and copy the contents here: </span>
<div><textarea class="w3-input w3-border" id="secret"></textarea>
<button id="set_code" class="w3-button w3-round-xxlarge w3-green" onclick='setSecret()'>Enter</button>
    </div>
<?php
exit();
}

if ($client_info[1] === false) {
    $url = get_auth_url($client_info[0]);
?>
<hr>
<h3 class="w3-center">Service needs authorization</h3>
<div><span>Click <a href="<?php echo $url;?>" target="_blank">HERE</a> to get authorization code</span></div>
<div><label>Enter Code Here:</label><input class="w3-input w3-border" id="code">
<button id="set_code" class="w3-button w3-round-xxlarge w3-green" onclick='authCode()'>Enter</button>
</div>
<?php
exit();
}

if (!isset($G_ROOTFOLDER) || $G_ROOTFOLDER === "") {
?>
<hr>
<h3 class="w3-center">Configure root folder</h3>
<div><label>Shared Drive Root Folder:</label><input class="w3-input w3-border" id="folder">
<button id="set_folder" class="w3-button w3-round-xxlarge w3-green" onclick='setFolder()'>Enter</button>
</div>
<?php
exit();
} else {
?>
<table class="w3-table w3-striped w3-border w3-white" id="file_table">
<tr><th class="w3-green w3-center">File</th><th class="w3-green w3-center">Last Modified</th></th>
<?php
}
?>
</table>
<script>
    loadFiles();
</script>
</div>
