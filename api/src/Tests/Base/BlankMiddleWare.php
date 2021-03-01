<?php

namespace App\Tests\Base;

use Slim\Http\Request;
use Slim\Http\Response;

class BlankMiddleWare
{

    /* BlankMiddleWare */

    public function __invoke(Request $request, Response $response, $next)
    {
        $request = $request->withAttribute('oauth2-token', ['user_id' => 1000]);
        return $next($request, $response);

    }


    /* End */
}
