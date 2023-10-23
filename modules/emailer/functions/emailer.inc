<?php namespace mailer;

/*.
    require_module 'standard';
    require_module 'filter';
.*/

require_once $FUNCTIONDIR."/divisional.inc";
require_once $FUNCTIONDIR."/database.inc";
require_once $BACKEND."/email.inc";

class Email
{

    private $user;

    private $event;

    private $eventID;

    private $departments;


    public static function allowed()
    {
        if (RBACApiRequest('site.email_lists')) {
            return true;
        }

        if (!class_exists('\\concom\\POSITION') || !method_exists('\\concom\\POSITION', 'getConComPosition')) {
            return false;
        }
        $user = $_SESSION['accountId'];
        $depts = \concom\POSITION::getConComPosition($user);
        foreach ($depts as $dept) {
            $dep = $dept['departmentId'];
            $pos = $dept['positionId'];

            $sql = "SELECT * FROM `EmailListAccess` WHERE `DepartmentID` "."= $dep AND `PositionID` = $pos";
            $result = \DB::run($sql);
            $value = $result->fetch();
            if ($value !== false) {
                return true;
            }
        }
        return false;

    }


    public function __construct()
    {
        $this->user = $_SESSION['accountId'];
        $this->eventID = current_eventID();
        if ($this->event === null) {
            return;
        }
        $this->event = current_eventName();
        $this->departments = [];
        $depts = \concom\POSITION::getConComPosition($this->user);
        foreach ($depts as $dept) {
            $this->departments[] = [
            'department' => $dept['department'],
            'departmentId' => $dept['departmentId'],
            'position' => $dept['positionId']
            ];
        }

    }


    public function canAdd()
    {
        if (RBACApiRequest('site.email_lists')) {
            return true;
        }
        return false;

    }


    public function buildFrom()
    {
        $result = [];
        foreach ($this->departments as $dept) {
            $dep = $dept['department'];
            if ($GLOBALS['Departments'][$dep]['Email']) {
                foreach ($GLOBALS['Departments'][$dep]['Email'] as $email) {
                    array_push($result, $email['EMail']);
                }
            }
        }
        return $result;

    }


    public function countList($where = '')
    {
        $sql = "SELECT COUNT(DISTINCT `Email`) AS count FROM `Members` WHERE `Email` IS NOT NULL AND NOT `Deceased` AND NOT `DoNotContact` AND NOT `EmailOptOut` AND `Email` LIKE '%@%'";
        if (!empty($where)) {
            $event = $this->eventID;
            $user = $this->user;
            eval('$where  = "'.str_replace('"', '\\"', $where).'";');
            $sql .= " AND ".$where;
        }
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value == false) {
            return 0;
        }
        return $value['count'];

    }


    public function constructList($where = '')
    {
        $sql = "SELECT DISTINCT replace(`Email`, ' ', '') AS Email FROM `Members` WHERE `Email` IS NOT NULL AND NOT `Deceased` AND NOT `DoNotContact` AND NOT `EmailOptOut` AND `Email` LIKE '%@%'";
        if (!empty($where)) {
            $event = $this->eventID;
            $user = $this->user;
            eval('$where  = "'.str_replace('"', '\\"', $where).'";');
            $sql .= " AND ".$where;
        }
        $sql .= " ORDER BY `Email` Asc;";
        $result = \DB::run($sql);
        $value = $result->fetch();

        if ($value == false) {
            return null;
        }

        $emails = [];
        while ($value !== false) {
            if (filter_var($value['Email'], FILTER_VALIDATE_EMAIL)) {
                $emails[] = $value['Email'];
            }
            $value = $result->fetch();
        }

        return $emails;

    }


    public function testSQL($sql)
    {
        $result = $this->constructList($sql);
        if ($result !== null) {
            return count($result);
        }
        return 0;

    }


    private function addListToAccess(&$access, $value)
    {
        if (array_key_exists($value['Name'], $access)) {
            return;
        }
        $event = $this->event;
        $user = $this->user;
        $value['DBName'] = $value['Name'];
        if (strpos($value['Name'], '$') !== false) {
            eval('$str  = "'.str_replace('"', '\\"', $value['Name']).'";');
            $value['Name'] = $str;
        }
        $value['DBDescription'] = $value['Description'];
        if (strpos($value['Description'], '$') !== false) {
            eval('$str  = "'.str_replace('"', '\\"', $value['Description']).'";');
            $value['Description'] = $str;
        }
        $access[$value['DBName']] = $value;

    }


    public function buildTo()
    {
        $response = [];
        if (RBACApiRequest('site.email_lists')) {
            $sql = "SELECT * FROM `EmailLists`";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $this->addListToAccess($response, $value);
                $value = $result->fetch();
            }
            return $response;
        } else {
            foreach ($this->departments as $dept) {
                $dep = $dept['departmentId'];
                $pos = $dept['position'];
                $sql = <<<SQL
    SELECT
        *
    FROM
        `EmailLists`
    WHERE
        EmailListID IN (
            SELECT
                EmailListID
            FROM
                `EmailListAccess`
            WHERE
                PositionID = $pos
                AND DepartmentID = $dep
        )
SQL;
                $result = \DB::run($sql);
                $value = $result->fetch();
                while ($value !== false) {
                    $this->addListToAccess($response, $value);
                    $value = $result->fetch();
                }
            }
            return $response;
        }

    }


    public function buildEdit()
    {
        $response = [];
        if (RBACApiRequest('site.email_lists')) {
            $sql = "SELECT * FROM `EmailLists`";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $this->addListToAccess($response, $value);
                $value = $result->fetch();
            }
            return $response;
        } else {
            foreach ($this->departments as $dept) {
                $dep = $dept['departmentId'];
                $pos = $dept['position'];
                $sql = <<<SQL
    SELECT
        *,
        (
            SELECT
                `ChangeAccess`
            FROM
                `EmailListAccess`
            WHERE
                EmailListID = d.EmailListID
                AND PositionID = $pos
                AND DepartmentID = $dep
        ) AS ChangeAccess
    FROM
        `EmailLists` AS d
    WHERE
        EmailListID IN (
            SELECT
                EmailListID
            FROM
                `EmailListAccess`
            WHERE
                PositionID = $pos
                AND DepartmentID = $dep
                AND EditList > 0
        )
SQL;
                $result = \DB::run($sql);
                $value = $result->fetch();
                while ($value !== false) {
                    $this->addListToAccess($response, $value);
                    $value = $result->fetch();
                }
            }
            return $response;
        }

    }


    public function getTo($key)
    {
        $sql = "SELECT `Code` FROM `EmailLists` WHERE `Name` = '$key'";
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value == false) {
            return null;
        }

        return $this->constructList($value['Code']);

    }


    public function getToById($id)
    {
        $sql = "SELECT `Code` FROM `EmailLists` WHERE `EmailListID` = '$id'";
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value == false) {
            return null;
        }

        return $this->constructList($value['Code']);

    }


    public function getToCount($id)
    {
        $sql = "SELECT `Code` FROM `EmailLists` WHERE `EmailListID` = '$id'";
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value == false) {
            return null;
        }

        return $this->countList($value['Code']);

    }


    public function appendList($original, $addition)
    {
        if (gettype($addition) === 'array') {
            $output = array_merge($original, $addition);
        } else {
            $output = array_push($original, $addition);
        }
        return array_unique($output);

    }


    public function updateList($data)
    {
        $name = \MyPDO::quote($data->name);
        $desc = \MyPDO::quote($data->description);
        $code = \MyPDO::quote($data->code);
        if (!empty($this->departments)) {
            $dept  = $this->departments[0]['departmentId'];
            $pos = $this->departments[0]['position'];
        } else {
            $dept = 0;
            $pos = 0;
        }

        $id = $data->id;
        if ($data->id < 0) {
            $sql = <<<SQL
    INSERT INTO `EmailLists` (
        `Name`, `Description`, `Code`)
    VALUES
        (
            $name, $desc, $code
        );
    INSERT INTO `EmailListAccess` (
        DepartmentID, PositionID, EmailListID, EditList, ChangeAccess)
    VALUES
        (
            $dept, $pos, last_insert_id(), 1, 1
        )
SQL;
        } else {
            $sql = <<<SQL
    UPDATE
        `EmailLists`
    SET
        `Name` = $name,
        `Description` = $desc,
        `Code` = $code
    WHERE
        `EmailListID` = $id
SQL;
        }
        \DB::run($sql);
        if ($data->access !== null) {
            $sql = "DELETE FROM `EmailListAccess` WHERE `EmailListID` = $id";
            \DB::run($sql);
            foreach ($data->access as $rule) {
                $id = $rule->DepartmentID;
                $pos = $rule->PositionID;
                $elist = $rule->EmailListID;
                $edit = $rule->EditList;
                $change = $rule->ChangeAccess;
                $sql = <<<SQL
                    INSERT INTO `EmailListAccess`(
                        `DepartmentID`, `PositionID`, `EmailListID`,
                        `EditList`, `ChangeAccess`
                    )
                    VALUES
                    (
                        $id, $pos, $elist, $edit, $change
                    )
                    ON DUPLICATE KEY UPDATE
                        `EditList` = $edit,
                        `ChangeAccess` = $change
SQL;
                \DB::run($sql);
            }
        }

    }


    public function getAccessControl($listID)
    {
        global $Departments;

        $output = [];
        $sql = <<<SQL
        SELECT
            (
                SELECT
                    `Name`
                FROM
                    `Departments`
                WHERE
                    `A`.`DepartmentID` = `DepartmentID`
            ) AS Department,
            (
                SELECT
                    `Name`
                FROM
                    `ConComPositions`
                WHERE
                    `A`.`PositionID` = `PositionID`
            ) AS Position,
            `A`.*
        FROM
            `EmailListAccess` as A
        WHERE
            `EmailListID` = $listID
SQL;
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value === false) {
            $sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$_ENV['DBNAME']."' AND `TABLE_NAME`='EmailListAccess';";
            $result = \DB::run($sql);
            $data = ['Department' => null,
            'Position' => null];
            $value = $result->fetch();
            while ($value !== false) {
                $data[$value['COLUMN_NAME']] = null;
                $value = $result->fetch();
            }
            $output[] = $data;
        } else {
            while ($value !== false) {
                $output[] = $value;
                $value = $result->fetch();
            }
        }
        return $output;

    }


    public function send($data)
    {
        $to = $this->getToById($data->to);
        if (count($to) > 0) {
            foreach ($to as $target) {
                \ciab\Email::mail($target, $data->from, $data->subject, $data->body, $data->reply, \ciab\Email::EMAIL_TYPE_HTML);
            }
        }

    }


    /* End Class */
}
