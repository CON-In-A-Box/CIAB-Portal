<?php

/*.
    require_module 'standard';
.*/


require_once($FUNCTIONDIR."/database.inc");


function get_tickets($id, $event = null)
{
    global $db;

    $sql = <<<SQL
        SELECT
            *,
            (
                SELECT
                    EventName
                FROM
                    `Events` AS e
                WHERE
                    r.EventID = e.EventID
            ) AS EventName
        FROM
            `Registrations` AS r
        WHERE
            (
                `RegisteredByID` = $id
                OR `AccountID` = $id
            )
SQL;
    if ($event !== null) {
        $sql .= "AND `EventID` = $event";
    }
    $sql .= ";";

    $badges = [];
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $badges[] = $value;
        $value = $result->fetch();
    }

    return $badges;

}
