<?php
/*
    Deploy out a GitHub branch automatically

    This is intended to be utilized with the GitHub Webhooks facility.
*/

/*.
    require_module 'standard';
    require_module 'json';
    require_module 'hash';
.*/

// Load in the .env file, if it exists
require __DIR__."/../vendor/autoload.php";
if (is_file(__DIR__.'/../.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__.'/../');
    $dotenv->load();
}

$secret = getenv('GITHUB_SECRET');
if ($secret === false) {
    die('GITHUB_SECRET not defined');
}
$branch = getenv('GITHUB_BRANCH');
if ($branch === false) {
    $branch = 'master';
}

header('Content-Type: text/plain');
set_time_limit(180);

if (empty($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    http_response_code(403);
    die('Access Denied - badsig');
}

$checksig = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE']);
if (2 !== count($checksig)) {
    http_response_code(400);
    die('Access Denied - malsig');
}

$algos = hash_algos();
list($algo, $hash) = $checksig;
if (!in_array($algo, $algos)) {
    http_response_code(400);
    die("Access Denied- badalgo: ".$algo);
}

$payload = @file_get_contents('php://input');
$expectedHash = hash_hmac($algo, $payload, $secret);
if ($expectedHash !== $hash) {
    http_response_code(403);
    die('Access Denied - sigfail');
}

try {
    $data = json_decode($payload, true);
} catch (Exception $e) {
    die('Unparsable json');
}
if ($data['ref'] === "refs/heads/$branch") {
    $workDir = dirname(__DIR__."/../");
    $command = "git -C $workDir pull 2>&1";
    exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        echo '[OK] '.$command."\n";
    } else {
        http_response_code(503);
        echo '[ERROR '.$returnCode.'] '.$command."\n";
    }
    echo '  - ';
    echo implode("\n  - ", $output);
    echo "\n";
    echo "Done";
} else {
    echo "Skipping Non-$branch Branch '".$data['ref']."'.\n";
}
