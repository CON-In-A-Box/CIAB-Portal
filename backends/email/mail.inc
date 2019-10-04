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
        $subject,
        $message,
        $additional_headers = null,
        $additional_parameters = null
    ) {
        return @mail(
            $to,
            $subject,
            $message,
            $additional_headers,
            $additional_parameters
        );

    }


    /* end class */
}
