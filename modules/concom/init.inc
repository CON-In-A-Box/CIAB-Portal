<?php

require_once(__DIR__.'/functions/VOLUNTEERS.inc');
require_once(__DIR__.'/functions/POSITION.inc');
require_once(__DIR__.'/functions/REGISTRATION.inc');

if (!isset($ConComPositions)) {
    $ConComPositions = array();
    $sql = "SELECT * FROM ConComPositions;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $ConComPositions[$value['PositionID']] = $value['Name'];
        $value = $result->fetch();
    }

    if (!array_key_exists('CONCOMHOURS', $GLOBALS) || !$GLOBALS['CONCOMHOURS']) {
        $ConComHours = 60;
    } else {
        $ConComHours = $GLOBALS['CONCOMHOURS'];
    }
}
