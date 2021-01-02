<?php declare(strict_types=1);

namespace App\Controller\Payment;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Stripe\Stripe;
use App\Controller\BaseController;

require_once __DIR__.'/../../../../functions/locations.inc';

class PostCheckout extends BaseController
{
    

    public function __construct(Container $container)
    {
        parent::__construct('registration', $container);

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $BASEURL;
        $successFunction = $request->getParsedBodyParam('successFunction');
        $storeSlug = $request->getParsedBodyParam('storeSlug');
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                  'name' => 'Adult Angry Ferret',
                ],
                'unit_amount' => 8500,
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $BASEURL.'/index.php?Function='.$successFunction.'&storeSlug='.$storeSlug.'&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $BASEURL.'/cancel',
          ]);
        
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        ['id' => $session->id ]
        ];

    }


    /* end PostCheckout */
}
