<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace Vendor\Payment;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\InternalServerErrorException;

class PostPayment extends BasePayment
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        global $_ENV;

        if (!array_key_exists('STRIPE_PRIVATE_KEY', $_ENV)) {
            throw new InternalServerErrorException('Stripe is not configured');
        }

        $required = ['success', 'cancel', 'cart'];
        $body = $this->checkRequiredBody($request, $required);

        $line_items = [];

        foreach ($body['cart'] as $item) {
            $line_items[] = [
            'price_data' => [
            'currency' => 'usd',
            'product_data' => [
            'name' => $item['name']
                    ],
            'unit_amount' => $item['price']
                ],
            'quantity' => $item['quantity']
            ];
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $body['success']."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $body['cancel']."?session_id={CHECKOUT_SESSION_ID}",
          ]);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [
        'processor' => 'Stripe',
        'session_id' => $checkout_session['id'],
        ],
        201
        ];

    }


    /* end PostPayment */
}
