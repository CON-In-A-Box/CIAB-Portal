<?php

/*.
    require_module 'standard';
.*/

/* This should only be accessable from volunteer staff */
if (!(isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS'])) {
    if (empty($_SESSION['customFields']['currConComPos'])) {
        goSite();
    }
}

require_once $FUNCTIONDIR."/volunteer.inc";

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'rewardId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'rewards'          => FILTER_UNSAFE_RAW,
    'validate_login'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'validate_passwd'  => FILTER_SANITIZE_ENCODED,
    'update_hour'      => FILTER_UNSAFE_RAW,
    'delete_hour'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'update_prize'     => FILTER_UNSAFE_RAW,
    'delete_prize'     => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_prize'        => FILTER_UNSAFE_RAW,
    'refundId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

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
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
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
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $item = json_decode($updateData['update_hour']);
            update_volunteer_hours($item->{'EntryID'}, null, $item->{'Actual Hours'}, $item->{'End Date Time'}, $item->{'Time Modifier'}, $item->{'Department Worked'}, null, $item->{'Authorized By'});
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['delete_hour'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            delete_volunteer_hours($updateData['delete_hour']);
            exit();
        }
    }

    if (isset($updateData['update_prize'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $item = json_decode($updateData['update_prize']);
            if ($item->{'RewardGroupID'} == 'new') {
                $group = add_volunteer_prize_group($item->{'GroupLimit'});
            } elseif ($item->{'RewardGroupID'} != '') {
                $group = $item->{'RewardGroupID'};
                update_volunteer_prize_group($group, $item->{'GroupLimit'});
            } else {
                $group = $item->{'RewardGroupID'};
            }
            update_volunteer_prize($item->{'PrizeID'}, $item->{'Name'}, $item->{'Value'}, ($item->{'Promo'} == 'yes'), $group, $item->{'TotalInventory'});
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['delete_prize'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            delete_volunteer_prize($updateData['delete_prize']);
            exit();
        }
    }

    if (isset($updateData['new_prize'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $item = json_decode($updateData['new_prize']);
            if ($item->{'RewardGroupID'} == 'new') {
                $group = add_volunteer_prize_group($item->{'GroupLimit'});
            } elseif ($item->{'RewardGroupID'} != '') {
                $group = $item->{'RewardGroupID'};
                update_volunteer_prize_group($group, $item->{'GroupLimit'});
            } else {
                $group = $item->{'RewardGroupID'};
            }
            new_volunteer_prize($item->{'Name'}, $item->{'Value'}, ($item->{'Promo'} == 'yes'), $group, $item->{'TotalInventory'});
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['refundId'])) {
        $data = json_decode($updateData['rewards'], true);
        $prizes = [];
        foreach ($data as $prize) {
            $prizes[] = $prize;
        }
        refund_prizes($updateData['refundId'], $prizes);
        exit();
    }
}
