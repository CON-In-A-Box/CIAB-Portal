<?php

namespace concom;

class POSITION
{


    public static function getConComPosition($account, $event = null)
    {
        if ($event == null) {
            $event = current_eventID();
        }
        $sql = <<<SQL
            SELECT
                *,
                (
                    SELECT
                        Name
                    FROM
                        Departments
                    WHERE
                        DepartmentID = c.DepartmentID
                ) as Department,
                (
                    SELECT
                        Name
                    FROM
                        ConComPositions
                    WHERE
                        PositionID = c.PositionID
                ) as Position
            FROM
                ConComList as c
            WHERE
                AccountID = $account
                AND EventID = $event;
SQL;
        $result = \DB::run($sql);
        $value = $result->fetch();
        $retvalue = null;
        while ($value !== false) {
            if ($retvalue == null) {
                $retvalue = array();
            }
            $retvalue[] = ['position' => $value['Position'],
            'department' => $value['Department'],
            'departmentId' => $value['DepartmentID'],
            'note' => $value['Note']];
            $value = $result->fetch();
        }
        return $retvalue;

    }


    /* end POSITION */
}
