<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions.inc");
require_once(__DIR__."/../database.inc");


function _getEvent($year)
{
    $date = "$year-1-1";
    $sql = "SELECT EventID FROM Events WHERE DateTo > '$date' LIMIT 1;";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value) {
        return $value['EventID'];
    }
    return null;

}


function _HistoricalListBuildSearch($page, $did)
{
    global $Neon;

    $conComFieldId = search_definedFields('CVGConCom Years Served');
    $criteria = '(';
    $year = intval(date("Y"));
    while ($year > 1900) {
        $criteria .= "'".$year."', ";
        $year--;
    }
    $criteria .= "'1900')";
    $search = [
    'method' => 'account/listAccounts',
    'criteria' => [
    [$conComFieldId, 'IN_RANGE', $criteria],
    ],
    'columns' => [
    'standardFields' => ['Account ID'],
    'customFields' => [$conComFieldId],
    ],
    'page' => [
    'currentPage' => $page,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
    ],
    ];
    $data = [];
    $result = $Neon->search($search);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $data['pages'] = $result['page']['totalPage'];
        $data['list'] = [];
        foreach ($result['searchResults'] as $val) {
            if (isset($val['CVGConCom Years Served'])) {
                $years = array_unique(explode("|", $val['CVGConCom Years Served']));
                foreach ($years as $year) {
                    $event = _getEvent($year);
                    $data['list'][] = "(".$val['Account ID'].", $did, 3, $event)";
                }
            }
        }
    }
    return $data;

}


function _insertHistorical($data)
{
    $sql = "REPLACE INTO ConComList (AccountID, DepartmentID, PositionID, EventID) VALUES";
    $sql .= implode(", ", $data);
    $sql .= ';';

    DB::run($sql);

}


function _build_HistoricalConComList($did)
{
    echo '<h2>Historical Current ConCom.</h2>';
    $i = 1;
    $runPage = 1;
    while ($i <= $runPage) {
        $result = _HistoricalListBuildSearch($i, $did);
        if (count($result['list'])) {
            _insertHistorical($result['list']);
            echo '<p> Processed '.count($result['list']).'</p>';
        }
        $runPage = $result['pages'];
        $i++;
    }
    echo "<h2>done<h2>";

}


function to_180924()
{
    $did = null;
    $sql = "SELECT `DepartmentID` FROM `Departments` WHERE Name = 'Historical Placeholder'";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value) {
        $did = $value['DepartmentID'];
    }

    if ($did == null) {
        $sql = <<<SQL
        INSERT INTO `Departments` (
            DepartmentID, Name, ParentDepartmentID
        ) VALUES (NULL, 'Historical Placeholder', 1);
        UPDATE
            `Departments` d1, `Departments` d2
        SET
            d1.`ParentDepartmentID` = d2.`DepartmentID`
        WHERE d1.Name = 'Historical Placeholder' AND
              d2.Name = 'Historical Placeholder';
        SELECT DepartmentID
        FROM `Departments`
        WHERE Name = 'Historical Placeholder';
SQL;
        $result = DB::run($sql);
        $value = $result->fetch();
        $did = $value['DepartmentID'];
    }

    print "<h2> db update </h2>";

    _build_HistoricalConComList($did);

}
