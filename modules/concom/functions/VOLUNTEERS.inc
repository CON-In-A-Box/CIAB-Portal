<?php

namespace concom;

class VOLUNTEERS
{


    public static function inVolunteers($lookup)
    {
        $sql = <<<SQL
            SELECT
                count(ListRecordID) as count
            From
                ConComList
            WHERE
                AccountID = $lookup
                AND DepartmentID = (
                    SELECT
                        DepartmentID
                    FROM
                        Departments
                    WHERE
                        Name = 'Volunteers'
                        AND ParentDepartmentID NOT IN (
                            SELECT
                                `DepartmentID`
                            FROM
                                `Departments`
                            WHERE
                                Name = 'Historical Placeholder'
                        )
                );
SQL;
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value !== false && $value['count']) {
            return true;
        } else {
            return false;
        }

    }


    /* end VOLUNTEERS */
}
