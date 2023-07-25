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

require_once __DIR__.'/../../functions/volunteer.inc';

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'validate_login'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'validate_passwd'  => FILTER_SANITIZE_ENCODED,
    'update_prize'     => FILTER_UNSAFE_RAW,
    'new_prize'        => FILTER_UNSAFE_RAW,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

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
}
