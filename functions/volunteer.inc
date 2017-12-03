<?php


function _vol_search(&$data, $page = 1)
{
    global $Neon;

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
    }

    $result = $Neon->go1($request);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $pages = $result['page']['totalPage'];
        foreach ($result['searchResults']['nameValuePairs'] as $record) {
            foreach ($record as $pair) {
                $rec = array();
                foreach ($pair as $key => $val) {
                    $rec[$val['name']] = $val['value'];
                }
            }
            array_push($data, $rec);
        }
        return $pages;
    } else {
        return 0;
    }

}


function get_volunteer_hours_for_user($id = null)
{
    $filter_callback = function ($entry) use ($id) {
        return $entry['VolunteerID_c.id'] == $id;
    };

    $sort_callback = function ($a, $b) use ($id) {
        if ($id == null && $a['VolunteerID_c.id'] != $b['VolunteerID_c.id']) {
            return (int)$a['VolunteerID_c.id'] - (int)$b['VolunteerID_c.id'];
        } else {
            return strtotime($a['EndDateTime_c']) - strtotime($b['EndDateTime_c']);
        }
    };

    $page = 0;
    $total_pages = 0;
    $data = array();
    do {
        $page++;
        $total_pages = _vol_search($data, $page);
        if ($id != null) {
            $data = array_filter($data, $filter_callback);
        }
    } while ($page < $total_pages);
    if (!empty($data)) {
        usort($data, $sort_callback);
    }
    return $data;

}


function record_volunteer_hours(
    $id,
    $hours,
    $end,
    $modifier,
    $department,
    $enterer,
    $authorized
) {
    global $Neon;

    $request = [
    'method' => 'customObjectRecord/createCustomObjectRecord',
    'parameters' => [
    'customObjectRecord.objectApiName' => 'VolHourEntries_c',
            ],
        ];

    $fields = ['VolunteerID_c' => (int)$id, 'ActualHoursWorked_c' => $hours,
    'EndDateTime_c' => $end, 'TimeModifier_c' => $modifier,
    'DepartmentWorked_c' => $department, 'EnteredBy_c' => $enterer,
    'AuthorizedBy_c' => $authorized];

    $index = 0;
    foreach ($fields as $key => $data) {
        $request['parameters']['customObjectRecord.customObjectRecordDataList.customObjectRecordData.name['.$index.']'] = $key;
        $request['parameters']['customObjectRecord.customObjectRecordDataList.customObjectRecordData.value['.$index.']'] = $data;
        $index++;
    }

    $result = $Neon->go1($request);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        return null;
    }

    return array_merge($request, $result);

}
