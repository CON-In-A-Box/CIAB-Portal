<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__."/../../../functions/session.inc";

class CiabMiddleware
{


    public function __invoke(Request $request, Response $response, $next)
    {
        if (isset($_SESSION)) {
            unset($_SESSION['IS_ADMIN']);
        }
        /* Here is where the accountId is grabbed for the API */
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if (!empty($user)) {
            \loadAccount($user);
            $_SESSION['accountId'] = $user;
        }
        $response = $next($request, $response);
        return $response;

    }


    /* End CiabMiddleware */
}
