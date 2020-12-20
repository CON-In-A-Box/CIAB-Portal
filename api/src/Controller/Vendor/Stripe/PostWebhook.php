<?php declare(strict_types=1);

namespace App\Controller\Vendor\Stripe;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Stripe\Stripe;
use App\Controller\BaseController;

class PostWebhook extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('registration', $container);

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        # We're not doing anything useful with webhooks just yet
        $body = $request->getParsedBody();
        error_log("STRIPE CALLBACK received ".$body['id']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        []
        ];

    }
}
