<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once(__DIR__."/database.inc");

if (!isset($ConComPositions)) {
    $ConComPositions = array();
    $sql = "SELECT * FROM ConComPositions;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $ConComPositions[$value['PositionID']] = $value['Name'];
        $value = $result->fetch();
    }
}


if (!isset($Departments)) {
    $Departments = array();
    $Divisions = array();

    $temp_dept = array();

    $sql = "SELECT * FROM Departments;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $temp_dept[$value['DepartmentID']] = ['name' => $value['Name'], 'parent' => $value['ParentDepartmentID']];
        $value = $result->fetch();
    }
    foreach ($temp_dept as $id => $dept) {
        $Departments[$dept['name']] = [];
        $Departments[$dept['name']]['Division'] = $temp_dept[$dept['parent']]['name'];
        $Departments[$dept['name']]['id'] = $id;
        $Departments[$dept['name']]['Email'] = [];

        if (in_array($Departments[$dept['name']]['Division'], $Divisions) == false) {
            array_push($Divisions, $Departments[$dept['name']]['Division']);
        }

        $sql = "SELECT * FROM EMails WHERE DepartmentID = ".$id.";";
        $result = $db->run($sql);
        $value = $result->fetch();
        while ($value !== false) {
            array_push($Departments[$dept['name']]['Email'], $value['EMail']);
            $value = $result->fetch();
        }
    }
    asort($Departments);
    asort($Divisions);
}


function getDivision($dep)
{
    return($GLOBALS['Departments'][$dep]['Division']);

}


function getDepartmentEmails($dep)
{
    return($GLOBALS['Departments'][$dep]['Email']);

}


function AddConComPosition($accountid, $department, $position, $note)
{
    $request = [
    'method' => 'account/retrieveIndividualAccount',
    'parameters' => [
    'accountId' => $accountid,
      ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $conComFieldId = search_definedFields('CVGConCom Current Position');

        $key = array_search($conComFieldId, array_column($result['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
        if ($key === false) {
            // Add the option since it doesn't exist.  Since arrays count from 0, the number of current entries
            // makes a perfect 'next' entry to use.
            $i = count($result['individualAccount']['customFieldDataList']['customFieldData']);
            $result['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $conComFieldId;
            $result['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $department.",".$position.",".$note;
        } else {
            // if the department is already assigned to this user, wipe it and replace it with the new info
            if (preg_match('/^'.$department.'/', $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue']) || preg_match('/\|'.$department.'/', $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'])) {
                RemoveConComPosition($accountid, $department, 'ALL');
                return AddConComPosition($accountid, $department, $position, $note);
            }
            $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] .= "|".$department.",".$position.",".$note;
        }

        $request = [
        'method' => 'account/editIndividualAccount',
        'parameters' => [
        'individualAccount.accountId' => $result['individualAccount']['accountId'],
        'individualAccount.primaryContact.contactId' => $result['individualAccount']['primaryContact']['contactId'],
        'individualAccount.primaryContact.firstName' => $result['individualAccount']['primaryContact']['firstName'],
        'individualAccount.primaryContact.lastName' => $result['individualAccount']['primaryContact']['lastName'],
         ],
        ];

        foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
            $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
            if (isset($val['fieldValue'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $val['fieldValue'];
            } elseif (isset($val['fieldOptionId'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = $val['fieldOptionId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = '';
            }
        }
        unset($val);
        unset($key);

        $result2 = $GLOBALS['Neon']->go1($request);


        if (isset($result2['operationResult']) && $result2['operationResult'] == 'SUCCESS') {
            // Notify the Division Director, so they know
            $to = getDepartmentEmails(getDivision($department))[0];
            $preferredFirstName = search_definedFields('Preferred First Name');
            $preferredLastName = search_definedFields('Preferred Last Name');
            $subject = 'ConCom Division addition to '.$department;
            if (empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'])) {
                $name = $result['individualAccount']['primaryContact']['firstName'];
            } else {
                $name = $result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'];
            }
            if (empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'])) {
                $name .= " ".$result['individualAccount']['primaryContact']['lastName'];
            } else {
                $name .= " ".$result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'];
            }
            $message = $_SESSION['fullName'].' has added '.$name." to ".$department.' as '.$position.'.';
            $headers = 'From: noreply@convergence-con.org'."\r\n";
            $headers .= 'Reply-To: noreply@convergence-con.org';
      
            mail($to, $subject, $message, $headers);

            return true;
        } else {
            print_r($result2);
            die("Failed to apply remove to Neon!");
        }
    }

}


function RemoveConComPosition($accountid, $department, $position)
{
    $request = [
    'method' => 'account/retrieveIndividualAccount',
    'parameters' => [
    'accountId' => $accountid,
    ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $conComFieldId = search_definedFields('CVGConCom Current Position');

        foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
            if (isset($val['fieldId']) && $val['fieldId'] == $conComFieldId) {
                // Remove only the position in question from the current list of concom positions
                $currentPos = '';
                $pos = explode('|', $val['fieldValue']);
                foreach ($pos as $entry) {
                    $field = explode(',', $entry);
                    if ($field[0] == $department && ($field[1] == $position || $position == 'ALL')) {
                        // This is the entry we are deleting
                    } else {
                        if (!empty($currentPos)) {
                            $currentPos .= '|';
                        }
                        $currentPos .= $entry;
                    }
                    unset($field);
                }
                if (empty($currentPos)) {
                    unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
                } else {
                    $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $currentPos;
                }
            }
        }

        $request = [
        'method' => 'account/editIndividualAccount',
        'parameters' => [
        'individualAccount.accountId' => $result['individualAccount']['accountId'],
        'individualAccount.primaryContact.contactId' => $result['individualAccount']['primaryContact']['contactId'],
        'individualAccount.primaryContact.firstName' => $result['individualAccount']['primaryContact']['firstName'],
        'individualAccount.primaryContact.lastName' => $result['individualAccount']['primaryContact']['lastName'],
         ],
        ];

        foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
            $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
            if (isset($val['fieldValue'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $val['fieldValue'];
            } elseif (isset($val['fieldOptionId'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = $val['fieldOptionId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = '';
            }
        }
        unset($val);
        unset($key);

        $result2 = $GLOBALS['Neon']->go1($request);

        if (isset($result2['operationResult']) && $result2['operationResult'] == 'SUCCESS') {
            // Notify the Division Director, so they know
            $to = getDepartmentEmails(getDivision($department))[0];
            $subject = 'ConCom Division Removal to '.$department;
            $preferredFirstName = search_definedFields('Preferred First Name');
            $preferredLastName = search_definedFields('Preferred Last Name');

            if (empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'])) {
                $name = $result['individualAccount']['primaryContact']['firstName'];
            } else {
                $name = $result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'];
            }
            if (empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'])) {
                $name .= " ".$result['individualAccount']['primaryContact']['lastName'];
            } else {
                $name .= " ".$result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'];
            }
            $message = $_SESSION['fullName'].' has removed '.$name." from ".$department.'.';
            $headers = 'From: noreply@convergence-con.org'."\r\n";
            $headers .= 'Reply-To: noreply@convergence-con.org';
      
            mail($to, $subject, $message, $headers);

            return true;
        } else {
            sendError("Failed to apply remove to Neon ".__FILE__, $result2);
        }
    } else {
        sendError("Retrieve failed ".__FILE__, $result);
    }

}


function ConComListBuildSearch()
{
    global $db;

    $sql = <<<SQL
        SELECT l.AccountID, COALESCE(l.Note, "") AS Note,
              (SELECT Name FROM Departments WHERE DepartmentID = l.DepartmentID) AS Department,
              (SELECT (CASE WHEN DepartmentID = ParentDepartmentID THEN 1 ELSE 0 END) FROM Departments WHERE DepartmentID = l.DepartmentID ) AS Divisional,
              (SELECT Name FROM ConComPositions WHERE PositionID = l.PositionID)   AS Position,
              (SELECT Name FROM ConventionYear WHERE YearID = l.YearID) AS Year
          FROM ConComList AS l;
SQL;

    $ids = array();
    $result = $db->run($sql);
    $value = $result->fetch();
    $db_staff = array();
    while ($value !== false) {
        $Division = getDivision($value['Department']);
        if ($value['Divisional']) {
            if ($value['Position'] == "Head") {
                $value['Department'] = "Division Director";
                $value['Position'] = "Director";
            } elseif ($value['Position'] == "Specialist") {
                $value['Department'] = "Division Support";
            }
        }

        if (array_key_exists($value['AccountID'], $db_staff)) {
            $entry = array('Account ID' => $value['AccountID'],
                      'Division' => $Division,
                      'Department' => $value['Department'],
                      'Position' => $value['Position'],
                      'Email' => '',
                      'First Name' => $value['AccountID'],
                      'Last Name' => '',
                      'Note' => $value['Note']);
            array_push($entry, $db_staff[$value['AccountID']]);
        } else {
            $db_staff[$value['AccountID']] = [[
                  'Account ID' => $value['AccountID'],
                  'Division' => $Division,
                  'Department' => $value['Department'],
                  'Position' => $value['Position'],
                  'Email' => '',
                  'First Name' => $value['AccountID'],
                  'Last Name' => '',
                  'Note' => $value['Note'],
                  ]];
        }

        if (!in_array($value['AccountID'], $ids)) {
            $ids[] = $value['AccountID'];
        }
        $value = $result->fetch();
    }

    $str = "('".implode("', '", $ids)."')";
    $users = lookup_users_by_ids($str);

    foreach ($users['users'] as $user) {
        if (array_key_exists($user['Id'], $db_staff)) {
            foreach ($db_staff[$user['Id']] as $entry_key => $entry) {
                $db_staff[$user['Id']][$entry_key]['First Name'] = $user['First Name'];
                $db_staff[$user['Id']][$entry_key]['Last Name'] = $user['Last Name'];
                $db_staff[$user['Id']][$entry_key]['Email'] = $user['Email'];
            }
        }
    }

    $output = [];
    // Flatten our multi-dimentional array
    foreach ($db_staff as $staff) {
        foreach ($staff as $entry) {
            $output[] = $entry;
        }
    }

    return $output;

}


function ConComListBuild()
{
    $fullConComList = array();

    foreach ($GLOBALS['Departments'] as $kdep => $dep) {
        if ($dep['Division'] != $kdep) {
            $fullConComList[] = [
            'Division' => $dep['Division'],
            'Department' => $kdep,
            'Position' => 'Department',
            'First Name' => '',
            'Last Name' => '',
            'Email' => implode("<br />", $dep['Email']),
            'Note' => '',
            'Account ID' => '',
            ];
        }
    }

    $db_staff = ConComListBuildSearch();
    $tmp = array_merge_recursive($fullConComList, $db_staff);
    return($tmp);

}


function DumpConComList()
{
    global $Divisions, $Departments;
  
    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Account",
    ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        foreach ($result['customFields']['customField'] as $val) {
            if ($val['fieldName'] == 'CVGConCom Current Position') {
                $conComFieldId = $val['fieldId'];
            }
        }
    } else {
        die("Failed to pull Custom Fields");
    }

    foreach ($Departments as $kdep => $dep) {
        //if($dep['Division'] != 'Corporate Staff' && $dep['Division'] != 'Committees') {
        echo $dep['Email'][0]."\n";
        //}
    }

    $i = 1;
    $runPage = 1;
    while ($i <= $runPage) {
        $search = [
        'method' => 'account/listAccounts',
        'criteria' => [
        [$conComFieldId, 'NOT_BLANK', ''],
        ],
        'columns' => [
        'standardFields' => ['Account ID', 'Email 1'],
        'customFields' => [$conComFieldId],
        ],
        'page' => [
        'currentPage' => $i,
        'pageSize' => 200,
        'sortColumn' => 'Account ID',
        'sortDirection' => 'ASC',
        ],
        ];
        $result = $GLOBALS['Neon']->search($search);

        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            $runPage = $result['page']['totalPage'];
            $i++;
            foreach ($result['searchResults'] as $val) {
                echo $val['Email 1']."\n";
            }
        }
    }
    foreach ($Divisions as $div) {
        echo getDepartmentEmails($div)[0]."\n";
    }

}


function ConComListDisplay()
{
    global $Divisions, $ConComPositions;

    $list = ConComListBuild();
    // Put each entry into a divisional/department mapped array
    foreach ($list as $line) {
        $printList[$line['Division']][$line['Department']][$line['Position']][$line['First Name']][$line['Last Name']] = $line['Email'].'|'.$line['Note'].'|'.$line['Account ID'];
    }
  
    // Sort everything so we don't have to later
    ksort_recursive($printList);
  
    // Start the crazy print routine!
    echo "<div class='w3-responsive'>";
    echo "<table class='w3-table-all w3-hoverable'>\n";
    echo '<tr id="table_head" class="w3-purple">';
    echo '<th class="w3-center">Department</th>';
    echo '<th class="w3-center">Division</th>';
    echo '<th class="w3-center">First Name</th>';
    echo '<th class="w3-center">Last Name</th>';
    echo '<th class="w3-center">Position</th>';
    echo '<th class="w3-center">Email</th>';
    echo '<th class="w3-center" colspan=2>Note</th>';
    echo "</tr>\n";
    foreach ($Divisions as $div) {
        $currentDivDirector = false;
        echo '<tr id="division" class="w3-purple"><th id="'.$div.'" class="w3-center" colspan=2>'.$div;
        if ($div != 'Committees' && $div != 'Corporate Staff') {
            echo ' Division';
        }
        echo '</th><th class="w3-center" colspan=3>';
        echo '<div class="w3-dropdown-hover w3-purple">';
        echo '  Go To Section';
        echo '  <div class="w3-dropdown-content w3-purple">';
        echo '    <a href="#main_nav" class="w3-border">Top of Page</a>';
        foreach ($Divisions as $menuDiv) {
            // ##==## Likely need to add some translation here to blot out '&'
            echo '    <a href="#'.$menuDiv.'" class="w3-border">'.$menuDiv.'</a>';
        }
        echo '  </div>';
        echo '</div>';
        echo '</th><th colspan=3>';
        echo implode('<br />', getDepartmentEmails($div));
        echo "</th></tr>\n";
        // Division Directors
        if (isset($printList[$div]['Division Director'])) {
            foreach ($printList[$div]['Division Director'] as $pos) {
                foreach ($pos as $kfName => $fName) {
                    foreach ($fName as $klName => $lName) {
                        $tmp = explode('|', $lName);
                        echo '<tr><td>Division Director</td><td>'.$div.'</td><td>'.$kfName.'</td><td>'.$klName.'</td><td>Director</td><td>'.getDepartmentEmails($div)[0].'</td>';
                        if (isset($_SESSION['IS_ADMIN'])) {
                            echo '<td>';
                        } else {
                            echo '<td colspan=2>';
                        }
                        echo $tmp[1];
                        if ($_SESSION['accountId'] == $tmp[2]) {
                            if (!empty($tmp[1])) {
                                echo '<br />';
                            }
                            echo 'This is you!';
                            $currentDivDirector = true;
                            if (isset($_SESSION['DIR_OF'])) {
                                if (!in_array($div, $_SESSION['DIR_OF'])) {
                                    $_SESSION['DIR_OF'][] = $div;
                                }
                            } else {
                                $_SESSION['DIR_OF'][] = $div;
                            }
                        }
                        echo "</td>";
                        // System Admins can remove anyone
                        if (isset($_SESSION['IS_ADMIN'])) {
                            echo '<td class="w3-red"><a href="index.php?Function=list&Remove='.urlencode($tmp[2]).'&Department='.urlencode($div).'&Position=Director" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$div.'?\');">Remove</a></td>';
                        }
                        echo "</tr>\n";
                    }
                    unset($tmp);
                }
            }
            unset($pos);
        }
        //Division Support
        if (isset($printList[$div]['Division Support'])) {
            foreach ($printList[$div]['Division Support'] as $pos) {
                foreach ($pos as $kfName => $fName) {
                    foreach ($fName as $klName => $lName) {
                        $tmp = explode('|', $lName);
                        echo '<tr><td>Division Support</td><td>'.$div.'</td><td>'.$kfName.'</td><td>'.$klName.'</td><td>Specialist</td><td>'.$tmp[0].'</td>';
                        if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector) {
                            echo '<td>';
                        } else {
                            echo '<td colspan=2>';
                        }
                        echo $tmp[1];
                        if ($_SESSION['accountId'] == $tmp[2]) {
                            if (!empty($tmp[1])) {
                                echo '<br />';
                            }
                            echo 'This is you!';
                        }
                        echo '</td>';
                        if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector) {
                            echo '<td class="w3-red"><a href="index.php?Function=list&Remove='.urlencode($tmp[2]).'&Department='.urlencode($div).'&Position=Specialist" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$div.'?\');">Remove</a></td>';
                        }
                        echo "</tr>\n";
                    }
                    unset($tmp);
                }
            }
            unset($pos);
        }
        if ($div != 'Committees' && $div != 'Corporate Staff' && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector)) {
            echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=list&AddDepartment='.$div.'">Add someone to '.$div.'</a></td></tr>'."\n";
        }
        // Departments
        foreach ($printList[$div] as $kdep => $dep) {
            $currentDepHead = false;
            if ($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') {
                echo '<tr id="department" class="w3-green">';
                if ($div == 'Committees') {
                    echo '<th id="'.$kdep.'" colspan=5>'.$kdep.'</th>';
                } else {
                    echo '<th id="'.$kdep.'">'.$kdep.'</th><th colspan=4>'.$div.'</th>';
                }
                echo '<th colspan=3>';
                echo implode('<br />', getDepartmentEmails($kdep));
                echo "</th></tr>\n";
            }
            if ($kdep != 'Division Director' && $kdep != 'Division Support') {
                foreach ($ConComPositions as $pos) {
                    if (isset($dep[$pos])) {
                        foreach ($dep[$pos] as $kfName => $fName) {
                            foreach ($fName as $klName => $lName) {
                                $tmp = explode('|', $lName);
                                echo '<tr>';
                                if ($div == 'Committees' || $div == 'Corporate Staff') {
                                    echo '<td colspan=2>'.$kdep.'</td>';
                                } else {
                                    echo '<td>'.$kdep.'</td>';
                                    echo '<td>'.$div.'</td>';
                                }
                                echo '<td>'.$kfName.'</td>';
                                if ($div == 'Corporate Staff' || $div == 'Committees') {
                                    echo '<td colspan=2>'.$klName.'</td>';
                                } else {
                                    echo '<td>'.$klName.'</td>';
                                    echo '<td>'.$pos.'</td>';
                                }
                                if ($div == 'Corporate Staff') {
                                    echo '<td>'.getDepartmentEmails($kdep)[0].'</td>';
                                } else {
                                    echo '<td>'.$tmp[0].'</td>';
                                }
                                if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != 'Head')) {
                                    echo '<td>';
                                } else {
                                    echo '<td colspan=2>';
                                }
                                echo $tmp[1];

                                // Check to see if this is an entry for the logged in person
                                if ($_SESSION['accountId'] == $tmp[2]) {
                                    if (!empty($tmp[1])) {
                                        echo '<br />';
                                    }
                                    echo 'This is you!';
                                    // Check to see if this person is a head of a department
                                    if ($pos == 'Head') {
                                        $currentDepHead = true;
                                        if (isset($_SESSION['HEAD_OF'])) {
                                            if (!in_array($kdep, $_SESSION['HEAD_OF'])) {
                                                $_SESSION['HEAD_OF'][] = $kdep;
                                            }
                                        } else {
                                            $_SESSION['HEAD_OF'][] = $kdep;
                                        }
                                    }
                                }
                                echo '</td>';
                                if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head")) {
                                    echo '<td class="w3-red"><a href="index.php?Function=list&Remove='.urlencode($tmp[2]).'&Department='.urlencode($kdep).'&Position='.$pos.'" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$kdep.'?\');">Remove</a></td>';
                                }

                                echo "</tr>\n";
                            }
                            unset($tmp);
                        }
                    }
                }
            }
            if (($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head"))) {
                echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=list&AddDepartment='.urlencode($kdep).'">Add someone to '.$kdep.'</a></td></tr>'."\n";
            }
        }
    }
    echo "</table>\n";
    echo "</div>";

}
