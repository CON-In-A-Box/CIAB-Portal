<?php

namespace App\Tests\Base;

use Slim\Http\Request;
use Slim\Http\Response;

class BlankMiddleWare
{

    /* BlankMiddleWare */

    public function __invoke(Request $request, Response $response, $next)
    {
        return $next($request, $response);

    }


    /* End */
}
