<?php

/*.
    require_module 'standard';
.*/

namespace ciab;

class CRM
{

    protected static $instance = null;


    protected function __construct()
    {

    }


    protected function __clone()
    {

    }


    public static function instance()
    {
        if (self::$instance === null) {
            /*.string.*/$client = null;
            if (array_key_exists('CRM_BACKEND', $GLOBALS)) {
                $client = $GLOBALS['CRM_BACKEND'];
            }
            if (!$client || empty($client)) {
                $client = 'none.inc';
            }
            $client = __DIR__."/CRM/".$client;
            if (!file_exists($client)) {
                error_log("CIAB ERROR: "."Requested CRM client does not exist! "."Unable to access CRM! Please correct settings!");
            }

            include_once($client);

            self::$instance = new backend\CRM();
        }

        return self::$instance;

    }


    public static function active()
    {
        return self::instance()->active();

    }


    public static function hasWebhooks()
    {
        return self::instance()->hasWebhooks();

    }


    public static function hasEvents()
    {
        return self::instance()->hasEvents();

    }


    public static function hasUpdates()
    {
        return self::instance()->hasUpdates();

    }


    public static function logout()
    {
        return self::instance()->logout();

    }


    public static function checkForUserAndPasswordMatch($user, $password)
    {
        return self::instance()->checkForUserAndPasswordMatch($user, $password);

    }


    public static function updateAccount($updateData)
    {
        return self::instance()->updateAccount($updateData);

    }


    public static function updateAccountAPI($member)
    {
        $updateData = [];
        $updateData['accountId'] = $member['id'];
        $updateData['contactId'] = $member['id'];
        $updateData['firstName'] = $member['legal_first_name'];
        $updateData['middleName'] = $member['middle_name'];
        $updateData['lastName'] = $member['legal_last_name'];
        $updateData['suffix'] = $member['suffix'];
        $updateData['email1'] = $member['email'];
        $updateData['email2'] = $member['email2'];
        $updateData['email3'] = $member['email3'];
        $updateData['phone1'] = $member['phone'];
        $updateData['phone2'] = $member['phone2'];
        $updateData['addressLine1'] = $member['address_line1'];
        $updateData['addressLine2'] = $member['address_line2'];
        $updateData['city'] = $member['city'];
        $updateData['state'] = $member['state'];
        $updateData['zipCode'] = $member['zip_code'];
        $updateData['zipPlus4'] = $member['zip_plus4'];
        $updateData['country'] = $member['country'];
        $updateData['province'] = $member['province'];
        $updateData['preferredFirstName'] = $member['preferred_first_name'];
        $updateData['preferredLastName'] = $member['preferred_last_name'];
        $updateData['conComDisplayPhone'] = $member['concom_display_phone'];

        self::instance()->updateAccount($updateData);

    }


    public static function importEvents()
    {
        return self::instance()->importEvents();

    }


    public static function importMeetings()
    {
        return self::instance()->importMeetings();

    }


    public static function importPeople()
    {
        return self::instance()->importPeople();

    }


    public static function lookupUsersByIds($ids, $single, $fields)
    {
        return self::instance()->lookupUsersByIds($ids, $single, $fields);

    }


    public static function lookupUsersByEmail($email, $single, $substring, $fields)
    {
        return self::instance()->lookupUsersByEmail($email, $single, $substring, $fields);

    }


    public static function lookupUsersByName($name, $single, $substring, $prefname, $fields)
    {
        return self::instance()->lookupUsersByName($name, $single, $substring, $prefname, $fields);

    }


    public static function lookupUsersByNameEmail($first, $last, $email, $fields, $single_user, $substring)
    {
        return self::instance()->lookupUsersByNameEmail($first, $last, $email, $fields, $single_user, $substring);

    }


    public static function lookupUserByLogin($user, $substring, $fields)
    {
        return self::instance()->lookupUserByLogin($user, $substring, $fields);

    }


    public static function registerWebhook($url, $name)
    {
        return self::instance()->registerWebhook($url, $name);

    }


    public static function removeWebhook($type)
    {
        return self::instance()->removeWebhook($type);

    }


    public static function update($from, $to)
    {
        return self::instance()->update($from, $to);

    }


    /* end class */
}
