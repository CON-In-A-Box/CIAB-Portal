<?php

namespace concom;

class VOLUNTEERS
{


    public static function inVolunteers($lookup)
    {
        $sql = "SELECT count(ListRecordID) as count From ConComList";
        $sql .= " WHERE AccountID = $lookup";
        $sql .= " AND DepartmentID = (SELECT DepartmentID FROM Departments WHERE Name = 'Volunteers');";
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
