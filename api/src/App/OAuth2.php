<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

use Chadicus\Slim\OAuth2\Routes;
use Slim\Views;

require_once __DIR__.'/../../../backends/oauth2.inc';

/* oauth2 stuff */

$renderer = new Views\PhpRenderer(__DIR__.'/../../../vendor/chadicus/slim-oauth2-routes/templates');

$app->map(['GET', 'POST'], Routes\Authorize::ROUTE, new Routes\Authorize($server, $renderer))->setName('authorize');
$app->post(Routes\Token::ROUTE, new Routes\Token($server))->setName('token');
$app->map(['GET', 'POST'], Routes\ReceiveCode::ROUTE, new Routes\ReceiveCode($renderer))->setName('receive-code');
