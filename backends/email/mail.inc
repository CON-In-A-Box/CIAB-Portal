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
        $additional_headers = null,
        $additional_parameters = null
    ) {
        $headers = $additional_headers;
        if ($headers === null) {
            $headers = "";
        } else {
            $headers .= "\r\n";
        }
        $headers .= "From: $from";
        if ($replyTo !== null) {
            $headers .= "\r\n";
            $headers .= "Reply-To: $replyTo";
        }
        return @mail(
            $to,
            $subject,
            $message,
            $headers,
            $additional_parameters
        );

    }


    /* end class */
}
