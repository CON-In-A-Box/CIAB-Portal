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
        `BadgesPickedUp` = `BadgesPickedUp` + 1
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
    $result['AddressLine1'] = $info['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine1'];
    $result['Phone'] = $info['individualAccount']['primaryContact']['phone1'];
    $result['Name'] = $info['individualAccount']['primaryContact']['firstName'].'&nbsp;'.$info['individualAccount']['primaryContact']['lastName'];
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
