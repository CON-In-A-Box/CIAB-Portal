<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions.inc");
require_once(__DIR__."/../database.inc");


function _ConComListBuildSearch($page)
{
    global $Neon;

    $conComFieldId = search_definedFields('CVGConCom Current Position');
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');

    $search = [
    'method' => 'account/listAccounts',
    'criteria' => [
    [$conComFieldId, 'NOT_BLANK', ''],
    ],
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
    'customFields' => [$conComFieldId, $preferredFirstName, $preferredLastName],
    ],
    'page' => [
    'currentPage' => $page,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
    ],
    ];
    $result = $Neon->search($search);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $data['pages'] = $result['page']['totalPage'];
        foreach ($result['searchResults'] as $val) {
            $positionsHeld = explode("|", $val['CVGConCom Current Position']);
            foreach ($positionsHeld as $pos) {
                $tmp = explode(",", $pos);
                $line = [
                'Account ID' => $val['Account ID'],
                'Department' => $tmp[0],
                'Position'   => $tmp[1],
                'Email'      => $val['Email 1'],
                ];

                if (empty($val['Preferred First Name'])) {
                    $line['First Name'] = $val['First Name'];
                } else {
                    $line['First Name'] = $val['Preferred First Name'];
                }

                if (empty($val['Preferred Last Name'])) {
                    $line['Last Name'] = $val['Last Name'];
                } else {
                    $line['Last Name'] = $val['Preferred Last Name'];
                }

                if (isset($tmp[2])) {
                    $line['Note'] = $tmp[2];
                } else {
                    $line['Note'] = "";
                }

                $data['list'][] = $line;
            }
            unset($tmp);
            unset($line);
            unset($pos);
            unset($positionsHeld);
        }
        unset($val);
    } else {
        print "Could not retrieve ConComList ".__FILE__." : ".$result;
    }
    return $data;

}


function _ConComListBuild()
{
    $fullConComList[] = [];
    $i = 1;
    $runPage = 1;
    while ($i <= $runPage) {
        $result = _ConComListBuildSearch($i);
        $tmp = array_merge_recursive($fullConComList, $result['list']);
        $fullConComList = $tmp;
        unset($tmp);
        $runPage = $result['pages'];
        $i++;
    }

    return($fullConComList);

}


function _build_ConComList()
{
    global $db;

    $year = current_yearID();

    $data = _ConComListBuild();
    print '<div id="update_staff" class="w3-container">';
    foreach ($data as $line) {
        if ($line['Account ID']) {
            /* Cover for a typo in the data */
            if ($line['Position'] == 'Specilist') {
                $id = 3;
            } elseif ($line['Position'] == 'Director') {
                $id = 1;
            } else {
                $sql = "SELECT COALESCE(PositionID, 0) as PositionID FROM ConComPositions WHERE Name = '".$line['Position']."';";
                $result = $db->run($sql);
                $value = $result->fetch();
                if ($value) {
                    $id = $value['PositionID'];
                } else {
                    print '<h2 style="color:red"> Record for '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].', Unknown position: '.$line['Position'].'</h2><br>';
                    $id = 0;
                }
            }

            $sql = "SELECT DepartmentID FROM Departments WHERE Name = '".$line['Department']."';";
            $result = $db->run($sql);
            $value = $result->fetch();
            if ($value) {
                $did = $value['DepartmentID'];
            } else {
                print '<h2 style="color:red"> Record for '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].', Unknown department: '.$line['Department'].'</h2><br>';
                $did = 0;
            }

            /* Do we have this record already? If so then DB is god */
            $sql = "SELECT AccountID FROM ConComList WHERE AccountID = ".$line['Account ID']." AND DepartmentID = ".$did." AND PositionID = ".$id.";";
            $result = $db->run($sql);
            $value = $result->fetch();
            if ($value) {
                print '<span>Record for '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].' already exists. Skip Import from Neon.</span><br>';
                continue;
            }

            if ($line['Note']) {
                $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, YearID, Note) VALUES (".$line['Account ID'].", ".$did.", ".$id.", $year, '".$line['Note']."');";
            } else {
                $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, YearID) VALUES (".$line['Account ID'].", ".$did.", ".$id.", $year);";
            }
            $db->run($sql);
            print '<span style="color:green">Imported '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].' from Neon.</span><br>';
        }
    }
    print '</div>';

}


function from_neon_to_152()
{
    _build_ConComList();

}
