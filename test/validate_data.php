<?php

require_once("../functions/config.inc");
require_once($FUNCTIONDIR . "/divisional.inc");
require_once($FUNCTIONDIR . "/allocations.inc");
require_once($FUNCTIONDIR . "/unit.inc");

function validate_divisional() {
    global $ConComPositions, $Departments, $Divisions;
    unit_ok(count($ConComPositions)>0, "No ConCom position loaded");
    unit_ok(count($Departments)>0, "No Departments loaded");
    unit_ok(count($Divisions)>0, "No Divisions loaded");
    foreach ($Departments as $key => $dept) {
        unit_ok(array_key_exists('Division', $dept), "No 'Division' in " . $key);
        unit_ok(array_key_exists('Email', $dept), "No 'Email' in " . $key);
    }
}

function validate_allocation() {
    global $AllocRooms, $AllocRoomTypes, $AllocHotels;
    unit_ok(count($AllocRooms)>0, "No AllocRooms loaded");
    unit_ok(count($AllocRoomTypes)>0, "No AllocRoomTypes loaded");
    unit_ok(count($AllocHotels)>0, "No AllocHotelsloaded");
    $prefixes = array();
    foreach ($AllocHotels as $key => $hotel) {
        unit_ok(array_key_exists('Location', $hotel), "No 'Location' in " . $key);
        unit_ok(array_key_exists('Phone', $hotel), "No 'Phone' in " . $key);
        unit_ok(array_key_exists('Prefix', $hotel), "No 'Prefix' in " . $key);
        array_push($prefixes, $hotel['Prefix']);
        unit_ok(array_key_exists('Contact', $hotel), "No 'Contact' in " . $key);
        unit_ok(array_key_exists('Email', $hotel), "No 'Email' in " . $key);
    }
    foreach ($AllocRooms as $prefix => $block) {
        unit_ok(in_array($prefix, $prefixes), "AllocRooms prefix not found in hotels");
        foreach ($block as $key1=>$subblock) {
            foreach ($subblock as $key2 => $room) {
                unit_ok(array_key_exists('Type', $room), "No 'Type' in [" . $key1.','.$key2.']');
                unit_ok(array_key_exists('Reserve', $room), "No 'Reserve' in [" . $key1.','.$key2.']');
            }
        }
    }
}

function run_test() {
    validate_divisional();
    validate_allocation();
}

run_test();
report();
