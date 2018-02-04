<?php

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'lookupId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'min_hour'         => FILTER_SANITIZE_NUMBER_INT,
    ];

    require_once($FUNCTIONDIR."/volunteer.inc");

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

    if (isset($updateData['min_hour'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
            $min = $updateData['min_hour'];
            $report = volunteer_minimum_hour_report($min);

            header("Content-Type: application/CSV; charset=UTF-8");
            header("Content-Language: en");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header('Content-Disposition: attachment; filename="hour_report.csv"');
            header("Pragma: no-cache");


            print implode(",", array_keys($report[0]));
            print "\n";
            foreach($report as $entry) {
                print implode(",", $entry);
                print "\n";
            }
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

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

    if (isset($updateData['update_prize'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
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
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
            delete_volunteer_prize($updateData['delete_prize']);
            exit();
        }
    }

    if (isset($updateData['new_prize'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
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
