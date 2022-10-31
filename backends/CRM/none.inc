<?php
/*
 *  This is a email back end module for NO CRM
 */

/*.
    require_module 'standard';
.*/

namespace ciab\backend;

class CRM
{


    public function __construct()
    {

    }


    public function active()
    {
        return false;

    }


    public function hasWebhooks()
    {
        return false;

    }


    public function hasEvents()
    {
        return false;

    }


    public function hasUpdates()
    {
        return false;

    }


    public function logout()
    {

    }


    public function importPeople()
    {

    }


    public function importEvents()
    {

    }


    public function importMeetings()
    {

    }


    public function checkForUserAndPasswordMatch($user, $password)
    {
        return false;

    }


    public function updateAccount($updateData)
    {

    }


    public function lookupUser($criteria, $single_result = false, $additional_fields = array(), $page = 1, &$output = null)
    {
        return array('code' => '404 Not Found', 'users' => array());

    }


    public function update($from, $to)
    {

    }


    /* end class */
}
