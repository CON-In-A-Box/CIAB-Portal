<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once $BASEDIR.'/library/google-api/vendor/autoload.php';


function create_client()
{
    global $G_CLIENT_SECRET, $G_CLIENT_CRED;

    if (!isset($G_CLIENT_SECRET) || strlen($G_CLIENT_SECRET) === 0) {
        return [null, false];
    }
    $secret = base64_decode($G_CLIENT_SECRET);
    if (!isset($G_CLIENT_CRED) || strlen($G_CLIENT_CRED) === 0) {
        $credentials = [];
    } else {
        $credentials = base64_decode($G_CLIENT_CRED);
    }
    $scope = implode(' ', array(Google_Service_Drive::DRIVE));

    $client = new Google_Client();
    $client->setScopes($scope);
    $client->setAuthConfig(json_decode($secret, true));
    $client->setAccessType('offline');
    $client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');

    if (!empty($credentials)) {
        $accessToken = json_decode($credentials, true);
        $client->setAccessToken($accessToken);
        return [$client, true];
    }
    return [$client, false];

}


function get_auth_url($client)
{
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();

    return $authUrl;

}


function verify_auth_url($client, $code)
{
    global $G_CLIENT_CRED;
    $authCode = trim($code);

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    $value = base64_encode(json_encode($accessToken));
    if (array_key_exists('G_CLIENT_CRED', $GLOBALS) && isset($G_CLIENT_CRED)) {
        set_conf_value('G_CLIENT_CRED', $value);
    } else {
        add_conf_value('G_CLIENT_CRED', $value);
    }

}


function get_drive_service($client)
{
    return new Google_Service_Drive($client);

}


function list_files($service)
{
    global $G_ROOTFOLDER;

    $output = [];
    $optParams = array(
      'pageSize' => '100',
      'q' => "'$G_ROOTFOLDER' in parents",
      'fields' => 'nextPageToken, files(id, name, trashed, modifiedTime, mimeType)'
    );

    try {
        $results = $service->files->listFiles($optParams);
    } catch (Exception $e) {
        return null;
    }
    foreach ($results as $entry) {
        if ($entry['trashed']) {
            continue;
        }
        if (!$entry['mimeType'] ||
             $entry['mimeType'] === 'application/vnd.google-apps.folder') {
            continue;
        }
        $output[] = [$entry['name'], $entry['modifiedTime'], $entry['id'],
        $entry['mimeType']];
    }

    while ($results['nextPageToken']) {
        $optParams['pageToken'] = $results['nextPageToken'];
        $results = $service->files->listFiles($optParams);
        foreach ($results as $entry) {
            if ($entry['trashed']) {
                continue;
            }
            if (!$entry['mimeType'] ||
                 $entry['mimeType'] === 'application/vnd.google-apps.folder') {
                continue;
            }
            $output[] = [$entry['name'], $entry['modifiedTime'], $entry['id'],
            $entry['mimeType']];
        }
    }
    sort($output);
    return $output;

}


function find_folder($folder)
{
    $client_info = create_client();
    $service = get_drive_service($client_info[0]);
    $q = "mimeType='application/vnd.google-apps.folder' and name='$folder'"." and not trashed";
    ;

    $response = $service->files->listFiles(array(
        'pageSize' => '5',
        'q' => $q,
        'fields' => 'nextPageToken, files(id, name, mimeType, parents, trashed)'
    ));
    if (count($response->files) !== 1) {
        return null;
    }
    $file = $response->files[0];
    return $file->id;

}
