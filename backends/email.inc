<?php
/*
 * Interface to the backend mailer
 */

namespace ciab;

class Email
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
            $client = $_ENV['EMAIL_BACKEND'];
            if (!$client || empty($client)) {
                $client = 'mail.inc';
            }
            $client = __DIR__."/email/".$client;
            if (!file_exists($client)) {
                error_log("CIAB ERROR: "."Requested E-mail client does not exist! "."Unable to send email! Please correct settings!");
                $client = __DIR__."/email/null.inc";
            }

            include_once($client);

            self::$instance = new backend\Email();
        }

        return self::$instance;

    }


    public static function mail(
        $to,
        $from,
        $subject,
        $message,
        $replyTo = null
    ) {
        return self::instance()->mail(
            $to,
            $from,
            $subject,
            $message,
            ($replyTo?$replyTo:$from)
        );

    }


    /* end class */
}
