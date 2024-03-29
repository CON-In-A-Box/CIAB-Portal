<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

/* This should only be accessable to CONCOM */
$concom = null;
if (class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
    $concom = \concom\POSITION::getConComPosition($_SESSION['accountId']);
    if (empty($concom)) {
        $concom = null;
    }
}
if (!(isset($_SESSION['IS_ADMIN']) || !($concom === null))) {
    goSite();
}

require_once(__DIR__.'/../../../functions/volunteer.inc');

$response = array();

$user_cache = array();


function lookup_user($user)
{
    global $user_cache;
    if (array_key_exists($user, $user_cache)) {
        return $user_cache[$user];
    } else {
        $output = lookup_users_by_key($user);
        $user_cache[$user] = $output;
        return $output;
    }

}


$file = fopen('php://input', 'r');
$lineno = 0;
while (!feof($file)) {
    $data = fgets($file);
    $lineno++;
    if ($data == false) {
        break;
    }
    $data = trim($data);
    $csv = str_getcsv($data);
    if (count($csv) === 9) {
        array_shift($csv);
    }
    if ($csv == null || $csv == false || count($csv) != 8) {
        $response[] = strval($lineno).': malformed: '.strval($data);
    } else {
        $event = null;
        if ($csv[6]) {
            $event = $csv[6];
        }
        if (is_numeric($csv[3])) {
            $csv[3] = intval($csv[3]);
        }
        /* user lookups */
        $user = null;
        $output = lookup_user($csv[0]);
        if (count($output['users']) != 1) {
            $response[] = strval($lineno).': failed: '.strval($data).': Unable to lookup user.';
            continue;
        }
        $user = $output['users'][0]['Id'];

        $output = lookup_user($csv[2]);
        if (count($output['users']) != 1) {
            $response[] = strval($lineno).': failed: '.strval($data).' : Unable to lookup authorizer.';
            continue;
        }
        $authorizer = $output['users'][0]['Id'];
        $output = lookup_user($csv[5]);
        if (count($output['users']) != 1) {
            $response[] = strval($lineno).': failed: '.strval($data).' : Unable to lookup enterer.';
            continue;
        }
        $enterer = $output['users'][0]['Id'];

        /* Fixup Hours */
        $when = date_parse($csv[4]);
        if ($when['year'] === false || $when['month'] === false ||
            $when['day'] === false || $when['hour'] === false ||
            $when['minute'] === false) {
            $response[] = strval($lineno).': failed: '.$csv[4].' : Invalid date format.';
            continue;
        }
        $date = sprintf(
            "%d-%02d-%02d %02d:%02d:%02d",
            $when['year'],
            $when['month'],
            $when['day'],
            $when['hour'],
            $when['minute'],
            $when['second']
        );

        $overlap = find_overlap_hours($date, $csv[1], $user);
        if ($overlap != null) {
            $response[] = strval($lineno).': overlap: '.strval($data).' : Hours Overlap with existing hours.';
            continue;
        }

        try {
            $result = record_volunteer_hours(
                $user,
                $csv[1],
                $date,
                $csv[7],
                $csv[3],
                $enterer,
                $authorizer,
                $event
            );
            $response[] = strval($lineno).': success: '.strval($data);
        } catch (Exception $e) {
            $response[] = strval($lineno).': failed: '.strval($data).' : '.$e->getMessage();
        }
    }
}
fclose($file);

echo json_encode($response);
exit();
