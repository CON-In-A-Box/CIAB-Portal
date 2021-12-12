<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace Vendor\Payment;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

use Stripe\Stripe;

abstract class BasePayment extends BaseController
{


    public function __construct(Container $container)
    {
        global $_ENV;

        parent::__construct('payment', $container);
        if (array_key_exists('STRIPE_PRIVATE_KEY', $_ENV)) {
            Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        }

    }


    /* End BasePayment */
}
