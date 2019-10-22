<?php
/*
 *  This is a email back end module using the build-in PHP mail()
 *  function.
 */

/*.
    require_module 'standard';
  .*/

namespace ciab\backend;

class Email
{


    public function __construct()
    {

    }


    public function mail(
        $to,
        $from,
        $subject,
        $message,
        $replyTo = null,
        $additional_headers = null
    ) {
        error_log("No e-mailer configured. Unable to send email "."('$to', '$subject')");

    }


    /* end class */
}
