<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php


function VolHoursSearch($page)
{
    $request = [
    'method' => 'customObjectRecord/listCustomObjectRecords',
    'parameters' => [
    'objectApiName' => 'VolHourEntries_c',
    'page.currentPage' => $page,
    'page.pageSize' => 200,
    ],
    ];

    $fields = ['VolunteerID_c.id', 'ActualHoursWorked_c', 'EndDateTime_c', 'TimeModifier_c', 'DepartmentWorked_c', 'EnteredBy_c', 'AuthorizedBy_c'];
    foreach ($fields as $key => $field) {
        $request['parameters']['customObjectOutputFieldList.customObjectOutputField.label['.$key.']'] = $field;
        $request['parameters']['customObjectOutputFieldList.customObjectOutputField.columnName['.$key.']'] = $field;
        echo "$field,";
    }
    echo "<br />\n";
    unset($key);
    unset($value);

    $result = $GLOBALS['Neon']->go1($request);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $return['pages'] = $result['page']['totalPage'];
        foreach ($result['searchResults']['nameValuePairs'] as $record) {
            foreach ($record as $field) {
                foreach ($field as $key => $val) {
                    echo $val['value'].",";
                }
                echo "<br />\n";
            }
        }
        return $return;
    } else {
        sendError("Fail to display Volunteer Hours ".__FILE__, $result);
    }

}


$i = 1;
$runPage = 1;
while ($i <= $runPage) {
    $result = VolHoursSearch($i);
    $runPage = $result['pages'];
    $i++;
}

?>
