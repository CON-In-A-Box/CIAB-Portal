<?php

global $ConComPositions;
global $ConComHours;

require_once(__DIR__.'/functions/VOLUNTEERS.inc');
require_once(__DIR__.'/functions/POSITION.inc');
require_once(__DIR__.'/functions/REGISTRATION.inc');
require_once(__DIR__.'/functions/LIST.inc');
require_once(__DIR__.'/functions/RBAC.inc');

if (!isset($ConComPositions)) {
    $ConComPositions = array();
    try {
        $sql = "SELECT * FROM ConComPositions;";
        $result = DB::run($sql);
        $value = $result->fetch();
        while ($value !== false) {
            $ConComPositions[$value['PositionID']] = $value['Name'];
            $value = $result->fetch();
        }
    } catch (Exception $e) {
    }

    if (!array_key_exists('CONCOMHOURS', $GLOBALS) || !$GLOBALS['CONCOMHOURS']) {
        $ConComHours = 60;
    } else {
        $ConComHours = $GLOBALS['CONCOMHOURS'];
    }
}
