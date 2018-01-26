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
    ];

    require_once($FUNCTIONDIR."/volunteer.inc");

    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['rewardId'])) {
        $data = json_decode($updateData['rewards'], true);
        $prizes = [];
        foreach($data as $prize) {
            $prizes[] = $prize['PrizeID'];
        }
        award_prizes($updateData['rewardId'], $prizes);
        echo $updateData['rewards'];
        exit();
    }
}
