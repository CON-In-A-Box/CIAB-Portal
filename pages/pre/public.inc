<?php

/*.
    require_module 'standard';
    require_module 'json';
    require_module 'pcre';
.*/

require __DIR__."/../../vendor/autoload.php";
require_once($FUNCTIONDIR."/authentication.inc");
require_once($FUNCTIONDIR."/session.inc");

use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request;
use \GuzzleHttp\Exception\RequestException;

/* functions */


function getAuthorizationHeader()
{
    /*. string .*/ $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER['HTTP_X_CIAB_API'])) {
        $headers = trim($_SERVER["HTTP_X_CIAB_API"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = @array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        } elseif (isset($requestHeaders['HTTP_X_CIAB_API'])) {
            $headers = trim($_SERVER["HTTP_X_CIAB_API"]);
        }
    }
    return $headers;

}


function getBearerToken()
{
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches) > 0) {
            return $matches[1];
        }
    }
    return null;

}


if (!empty($_REQUEST['signout'])) {
    LogoutUser();
    goSite();
    exit();
}

$token = getBearerToken();
if ($token === null) {
    if (array_key_exists('apiAuth', $_SESSION)) {
        $token = $_SESSION['apiAuth'];
    }
}
if ($token !== null) {
    if (array_key_exists('API_SERVER', $_ENV)) {
        $service_url = $_ENV['API_SERVER'].'/api/member/';
    } elseif (isset($WEBSERVER)) {
        $service_url = $WEBSERVER.'/api/member/';
    } else {
        $service_url = 'http://localhost//api/member/';
    }
    $client = new Client();
    try {
        $request = new Request('GET', $service_url, ['Authorization' => 'Bearer '.$token]);
        $resp = $client->send($request);
    } catch (RequestException $e) {
        error_log('oauth token authorization failed');
        LogoutUser();
        goSite();
        exit();
    }
    $response = json_decode($resp->getBody());
    /* Here is where we setup the base session data based on the authorized user */
    if ($resp->getStatusCode() == 200) {
        $_SESSION['accountId'] = $response->id;
        $_SESSION['apiAuth'] = $token;
        loadAccount($response->id);
        goSite();
        exit();
    }
}
