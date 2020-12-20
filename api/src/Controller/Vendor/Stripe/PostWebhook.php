<?php declare(strict_types=1);

namespace App\Controller\Vendor\Stripe;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;


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
        $logger = new Logger('stripe-webhook-logger');
        $handler = new RotatingFileHandler('/tmp/php-logs/stripe-webhooks');
        $logger->pushHandler($handler);
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_PRIVATE_KEY']);

        # We're not doing anything useful with webhooks just yet
        $body = $request->getParsedBody();
        $logger->info("STRIPE CALLBACK received ".$body['id']);

        $event = $stripe->events->retrieve($body['id']);

        # TODO: Raise a red flag if retrieve fails because the ID doesn't exist. It means we got spoofed.
        # TODO: fork out to modules based on what kind of event this was, e.g. process a successful reg payment, an unsuccessful one, etc.
        #       this should be modular, like everything else we're trying to do here.
        $logger->info($event);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        []
        ];

    }
}
