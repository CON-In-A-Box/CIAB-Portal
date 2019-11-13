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


if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'min_hour'         => FILTER_SANITIZE_NUMBER_INT,
    'dept_report'      => FILTER_SANITIZE_NUMBER_INT,
    'dept_name'        => FILTER_SANITIZE_SPECIAL_CHARS,
    'generateCSV'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'CSVfromSQL'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'name'             => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    require_once($FUNCTIONDIR."/volunteer.inc");
    require_once($FUNCTIONDIR."/database.inc");

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['min_hour'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $min = $updateData['min_hour'];
            $report = volunteer_minimum_hour_report($min);
            output_csv($report, "hour_report.csv");
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['dept_report'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $dept = $updateData['dept_report'];
            $name = $updateData['dept_name'];
            $event = current_eventID();
            $report = volunteers_in_dept(intval($dept), $event);
            output_csv($report, "department_report_".$name."_".$event.".csv");
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['generateCSV'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            csv_headers($updateData['generateCSV'].'.csv');
            print dump_table_to_csv($updateData['generateCSV']);
            exit();
        }
    }

    if (isset($updateData['CSVfromSQL'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            csv_headers($updateData['name'].'.csv');
            $sql = $updateData['CSVfromSQL'];
            print dump_sql_to_csv($updateData['CSVfromSQL'], true);
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
