<?php

/*.
    require_module 'standard';
.*/


require_once($FUNCTIONDIR."/database.inc");
require_once($FUNCTIONDIR."/users.inc");


function get_tickets($id, $event = null)
{
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
            ) AS EventName,
            (
                SELECT
                    Name
                FROM
                    `BadgeTypes` AS t
                WHERE
                    t.BadgeTypeID = r.BadgeTypeID
            ) AS Badge,
            (
                SELECT
                    BackgroundImage
                FROM
                    `BadgeTypes` AS t
                WHERE
                    t.BadgeTypeID = r.BadgeTypeID
            ) AS BackgroundImage
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
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $badges[] = $value;
        $value = $result->fetch();
    }

    return $badges;

}


function increment_badge($data)
{
    $sql = <<<SQL
    UPDATE
        `Registrations`
    SET
        `BadgesPickedUp` = IF(BadgesPickedUp IS NULL, 1, `BadgesPickedUp` + 1)
    WHERE
        `RegistrationID` = $data->RegID
SQL;
    DB::run($sql);

}


function print_badge($data)
{
    $csv = array();
    $csv[] = ["Badge Type", "Badge File BackGround", "Badge Number",
    "Badge Name", "Workstation ID", "Print Number"];
    $csv[] = [$data->{'Badge Type'}, $data->BackgroundImage, $data->Id,
    $data->{'Badge Name'}, $data->Workstation,
    $data->{'Print Count'}];

    increment_badge($data);

    $id = $data->Id;
    $now = time();
    $workstation = $data->Workstation;
    output_csv($csv, "badge_${id}_${now}_${workstation}.csv");

}


function update_badge($data)
{
    $name = MyPDO::quote($data->{'Badge Name'});
    $sql = <<<SQL
    UPDATE
        `Registrations`
    SET
        `BadgeName` = $name
    WHERE
        `RegistrationID` = $data->RegID
SQL;
    DB::run($sql);

}


function get_verification_info($accountID)
{
    $info = pullIndividualAccount($accountID);
    $result['AddressLine1'] = $info['AddressLine1'];
    $result['Phone'] = $info['Phone'];
    $result['Name'] = $info['FirstName'].'&nbsp;'.$info['LastName'];
    return $result;

}


function in_registration()
{
    if (isset($_SESSION['IS_ADMIN'])) {
        return true;
    }
    if (class_exists('\\concom\\REGISTRATION') && method_exists('\\concom\\REGISTRATION', 'inRegistration')) {
        return concom\REGISTRATION::inRegistration($_SESSION['accountId']);
    } else {
        return false;
    }

}


function registration_report($event = null, $clause = null)
{
    if ($event === null) {
        $event = \current_eventID();
    }

    return <<<SQL
        SELECT
            bt.`Name` BadgeType,
            COUNT(DISTINCT rt.`RegistrationID`) AS TotalSold,
            (
            SELECT
                COUNT(`RegistrationID`)
            FROM
                `Registrations`
            WHERE
                `BadgesPickedUp` > 0
                AND BadgeTypeId = bt.BadgeTypeID
                AND EventID = '$event'
        ) AS TotalPickedUp
        FROM
            `BadgeTypes` bt
        INNER JOIN `Registrations` rt USING(BadgeTypeID)
        WHERE
            rt.EventID = '$event'
        GROUP BY
            bt.BadgeTypeID

SQL;

}


function report_memberships_sold($event = null)
{
    if ($event === null) {
        $event = \current_eventID();
    }

    return <<<SQL
        SELECT
            rt.`AccountID` AccountID,
            mt.`PreferredFirstName` PreferredFirstName,
            mt.`PreferredLastName` PreferredLastName,
            mt.`FirstName` FirstName,
            mt.`LastName` LastName,
            bt.`Name` BadgeType,
            bt.`Cost` BadgeCost,
            rt.`RegistrationDate` RegistrationDate
        FROM
            `Registrations` rt
        INNER JOIN `BadgeTypes` bt USING(BadgeTypeID)
        INNER JOIN `Members` mt USING(AccountID)
        WHERE
            rt.EventID = '$event'
SQL;

}


function report_sold_summary($event = null)
{
    if ($event === null) {
        $event = \current_eventID();
    }

    return <<<SQL
        SELECT
            bt.`Name` BadgeType,
            bt.`Cost` BadgeCost,
            COUNT(DISTINCT rt.`RegistrationID`) AS TotalSold,
            (
                bt.`Cost` * COUNT(DISTINCT rt.`RegistrationID`)
            ) AS TotalRevenue
        FROM
            `BadgeTypes` bt
        INNER JOIN `Registrations` rt USING(BadgeTypeID)
        WHERE
            rt.EventID = '$event'
        GROUP BY
            bt.BadgeTypeID
SQL;

}
