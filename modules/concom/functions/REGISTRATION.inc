<?php

namespace concom;

class REGISTRATION
{


    public static function inRegistration($lookup)
    {
        $sql = "SELECT count(ListRecordID) as count From ConComList";
        $sql .= " WHERE AccountID = $lookup";
        $sql .= " AND DepartmentID = (SELECT DepartmentID FROM Departments WHERE Name = 'Registration');";
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value !== false && $value['count']) {
            return true;
        } else {
            return false;
        }

    }


    /* end REGISTRATION */
}
