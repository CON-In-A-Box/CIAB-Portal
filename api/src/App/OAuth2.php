<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

use Chadicus\Slim\OAuth2\Routes;
use Slim\Views;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__.'/../../../backends/oauth2.inc';

/* oauth2 stuff */

$renderer = new Views\PhpRenderer(__DIR__.'/../Templates');

class MyUserIdProvider implements Routes\UserIdProviderInterface
{


    public function getUserId(ServerRequestInterface $request, array $arguments = [])
    {
        $body = $request->getParsedBody();
        return isset($body['user_id']) ? $body['user_id'] : null;

    }


    /* End MyUserIdProvider */
}


$app->map(['GET', 'POST'], Routes\Authorize::ROUTE, new Routes\Authorize($server, $renderer, 'authorize.phtml', new MyUserIdProvider))->setName('authorize');
$app->post(Routes\Token::ROUTE, new Routes\Token($server))->setName('token');
$app->map(['GET', 'POST'], Routes\ReceiveCode::ROUTE, new Routes\ReceiveCode($renderer))->setName('receive-code');
