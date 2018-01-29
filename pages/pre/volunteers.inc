<?php

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'lookupId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (!empty($updateData['lookupId'])) {
        require_once($FUNCTIONDIR."/users.inc");

        $data = lookup_users_by_key($updateData['lookupId']);

        if (!$data['code']) {
            $user = $data['users'];
            echo json_encode($user[0]);
        } else {
            header("HTTP/1.0 ".$data['code']);
        }
        exit();
    }

    $arguments = [
    'rewardId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'rewards'          => FILTER_UNSAFE_RAW,
    'validate_login'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'validate_passwd'  => FILTER_SANITIZE_ENCODED,
    'update_hour'      => FILTER_UNSAFE_RAW,
    'delete_hour'      => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    require_once($FUNCTIONDIR."/volunteer.inc");

    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['rewardId'])) {
        $data = json_decode($updateData['rewards'], true);
        $prizes = [];
        foreach ($data as $prize) {
            $prizes[] = $prize['PrizeID'];
        }
        award_prizes($updateData['rewardId'], $prizes);
        echo $updateData['rewards'];
        exit();
    }

    require_once($FUNCTIONDIR."/authentication.inc");

    if (isset($updateData['validate_login'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
            if (doUserAndPasswordMatch($updateData['validate_login'], urldecode($updateData['validate_passwd']))) {
                setcookie("CIAB_VOLUNTEERADMIN", true);
                exit;
            } else {
                header("HTTP/1.0 401");
                unset($_COOKIE["CIAB_VOLUNTEERADMIN"]);
                exit();
            }
        }
    }

    if (isset($updateData['update_hour'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
            $item = json_decode($updateData['update_hour']);
            update_volunteer_hours($item->{'EntryID'}, null, $item->{'Actual Hours'}, $item->{'End Date Time'}, $item->{'Time Modifier'}, $item->{'Department Worked'}, null, $item->{'Authorized By'});
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['delete_hour'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
            delete_volunteer_hours($updateData['delete_hour']);
            exit();
        }
    }
}
