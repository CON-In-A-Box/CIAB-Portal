<?php

// Hotel Room Allocations
$AllocRooms = array();
$AllocRoomTypes = array();
$AllocHotels = array();

// Load the hotel data
if (isset($DATADIR)) {
    $hotels = array_filter(scandir($DATADIR.'/hotels'), function ($item) {
        global $DATADIR;
        $ext = pathinfo($item, PATHINFO_EXTENSION);
        return (!is_dir($DATADIR.'/hotels/'.$item) && $ext == 'json');
    });
    foreach ($hotels as $hotel) {
        $hotel_data = file_get_contents($DATADIR.'/hotels/'.$hotel);
        $hotel_json = json_decode($hotel_data, true);
        foreach ($hotel_json['Hotel'] as $key => $value) {
            $AllocHotels[$key] = $value;
        }
        foreach ($hotel_json['Rooms'] as $key => $value) {
            $AllocRooms[$key] = $value;
        }
        foreach ($hotel_json['RoomTypes'] as $type) {
            if (!in_array($type, $AllocRoomTypes)) {
                array_push($AllocRoomTypes, $type);
            }
        }
    }
} else {
    error_log('DATADIR undefined. No Hotel data found.');
}


function sendChangeToHotel($room)
{
    /*  Pending Function - not tested
    // send add update to the hotel
    $assignedRoomFieldId = search_definedFields('Room Assigned');
    $checkInFieldId = search_definedFields('Check In');
    $checkOutFieldId = search_definedFields('Check Out');
    $other1FieldId = search_definedFields('Other In Room 1');
    $other2FieldId = search_definedFields('Other In Room 2');
    $other3FieldId = search_definedFields('Other In Room 3');

    $search = [ 
    'method' => 'account/listAccounts', 
    'criteria' => [
    [$assignedRoomFieldId, 'CONTAIN', $room],
    ],
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Phone 1', 'Email 1'],
    'customFields' => [$checkInFieldId, $checkOutFieldId, $other1FieldId, $other2FieldId, $other3FieldId],
    ],
    'page' => [
    'currentPage' => 1,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
    ],
    ];
    $result = $GLOBALS['Neon']->search($search);

    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    $roomPrefix = explode("-", $room);
    foreach($AllocHotels as $hkey => $hotel) {
    if($roomPrefix[0] == $hotel['Prefix']) {
    $to = $AllocHotels[$hkey]['Email'];
    $contact = $AllocHotels[$hkey]['Contact'];
    }
    }
    $subject = "New room assignment for " . $room;
    $headers = 'From: allocations@convergence-con.org' . "\r\n";
    $headers .= 'Reply-To: allocations@convergence-con.org' . "\r\n";
    //    $headers .= 'Cc: allocations@convergence-con.org';
    $message = "Dear " . $contact . ",\n";
    $message .= "Please assign room " . $room . " to " . $firstName . " " . $lastName . ".\n";
    $message .= "Check In: " . $checkIn . ", Check Out: " . $checkOut . "\n";
    $message .= "Others in room: " . $other1 . ", " . $other2 . ", " . $other3 . "\n\n";
    $message .= 'ARR_DATE,ARR_TIME,DEP_DATE,TITLE,FIRST_NAME,LAST_NAME,NUM_ROOMS,ROOM_TYPE,ROOM_RATE,DEPOSIT_AMT,NON_SMOKE,SPREQ_1,SPREQ_2,SPREQ_3,SPREQ_4,NUM_ADULTS,NUM_CHILD,HOME_PHONE,BUS_PHONE,FAX_NUM,EMAIL_ADD,COMPANY,ADDRESS,ADDRESS2,CITY,STATE,ZIP,COUNTRY,HOTEL_COMMENTS,RES_COMMENTS,CC_NAME,CC_NUMBER,CC_EXPIRE,TITLE2,FIRST_NAME2,LAST_NAME2,TITLE3,FIRST_NAME3,LAST_NAME3,TITLE4,FIRST_NAME4,LAST_NAME4,HHONORS_NUM,SUB_GROUP_NUM,TA_IATA_NUM,TP_TRACKING_NUM' . "\n";
    $message .= $checkInDate . ",15:00," . $checkOutDate . ",," . $firstName . "," . $lastName . ",1,,,,,,,,,," . $phone . ",,," . $email  . ",," . $addr1 . "," . $addr2 . "," $city . "," . $state . "," . $zip . "," . $country . "," . $room . ",Do Not Move - " . $accountId . ",,,,,," . $other1 . ",,," . $other2 . ",,," . $other3 . "\n\n";
    mail($to, $subject, $message, $headers);
    } else {
    sendError("Room Search Fail", $result);
    }
    */

}


function updateRoomsInfo($page)
{
    $assignedRoomFieldId   = search_definedFields('Room Assigned');
    $overrideCatFieldId    = search_definedFields('Hotel Override Category');
    $overrideRoomFieldId   = search_definedFields('Hotel Override');
    $lotteryStatusFieldId  = search_definedFields('Lottery Status');
    $confirmationFieldId   = search_definedFields('Hotel Room Confirmation');
    $qualifierFieldId      = search_definedFields('Hotel Qualifier');
    $roomPreferenceFieldId = search_definedFields('Bed Preference');
    $checkInFieldId        = search_definedFields('Check In');
    $checkOutFieldId       = search_definedFields('Check Out');
    $other1FieldId         = search_definedFields('Other In Room 1');
    $other2FieldId         = search_definedFields('Other In Room 2');
    $other3FieldId         = search_definedFields('Other In Room 3');

    $search = [
    'method' => 'account/listAccounts',
    'criteria' => [
    [$assignedRoomFieldId, 'NOT_BLANK', ''],
    ],
    'columns' => [
    'standardFields' => [
    'Account ID',
    'First Name',
    'Last Name',
    'Phone 1 Full Number (F)',
    'Email 1'
      ],
    'customFields' => [
    $assignedRoomFieldId,
    $overrideCatFieldId,
    $overrideRoomFieldId,
    $lotteryStatusFieldId,
    $confirmationFieldId,
    $qualifierFieldId,
    $roomPreferenceFieldId,
    $checkInFieldId,
    $checkOutFieldId,
    $other1FieldId,
    $other2FieldId,
    $other3FieldId,
      ],
    ],
    'page' => [
    'currentPage' => $page,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
    ],
    ];
    $result = $GLOBALS['Neon']->search($search);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $return['pages'] = $result['page']['totalPage'];

        foreach ($result['searchResults'] as $val) {
            $rooms = explode('|', $val['Room Assigned']);
            unset($val['Room Assigned']); // Don't keep the raw room info in alloc, if changes happen, it would be wrong until refresh
            foreach ($rooms as $room) {
                $return['list'][$room] = $val;
            }
            unset($rooms);
        }
    } else {
        sendError("Could not retrieve RoomList ".__FILE__, $result);
        $return = false;
    }
    return $return;

}


function populateAllocInfo()
{
    $fullRoomList = [];
    $i = 1;
    $runPage = 1;
    while ($i <= $runPage) {
        $result = updateRoomsInfo($i);
        $tmp = array_merge_recursive($fullRoomList, $result['list']);
        $fullRoomList = $tmp;
        unset($tmp);
        $runPage = $result['pages'];
        $i++;
    }
    return $fullRoomList;

}


function totalAvailableRooms($hotel)
{
    global $AllocRooms;
    if (empty($_SESSION['AllocInfo'])) {
        $_SESSION['AllocInfo'] = populateAllocInfo();
    }
    $counts = [];
    foreach ($AllocRooms[$hotel] as $floor => $rooms) {
        foreach ($rooms as $room => $set) {
            @++$counts['Totals']['Total'];
            @++$counts['Totals']['Type'][$set['Type']];
            $roomLabel = $hotel.'-'.$floor.$room;
            if (isset($_SESSION['AllocInfo'][$roomLabel])) {
                @++$counts['Assigned']['Total'];
                @++$counts['Assigned']['Type'][$set['Type']];
                if (isset($_SESSION['AllocInfo'][$roomLabel]['Hotel Override Category'])) {
                    @++$counts['Totals']['Reserve'][$_SESSION['AllocInfo'][$roomLabel]['Hotel Override Category']];
                    @++$counts['Assigned']['Reserve'][$_SESSION['AllocInfo'][$roomLabel]['Hotel Override Category']];
                } else {
                    @++$counts['Totals']['Reserve']['Unreserved'];
                    @++$counts['Assigned']['Reserve']['Unreserved'];
                }
            } else {
                @++$counts['Unassigned']['Total'];
                @$counts['Rooms']['Total'][] = $hotel.'-'.$floor.$room;
                @++$counts['Unassigned']['Type'][$set['Type']];
                @$counts['Rooms']['Type'][$set['Type']][] = $hotel.'-'.$floor.$room;
                if (isset($set['Reserve'])) {
                    @++$counts['Totals']['Reserve'][$set['Reserve']];
                    @++$counts['Unassigned']['Reserve'][$set['Reserve']];
                } else {
                    @++$counts['Totals']['Reserve']['Unreserved'];
                    @++$counts['Unassigned']['Reserve']['Unreserved'];
                }
            }
        }
    }
    ksort_recursive($counts);
    return $counts;

}
