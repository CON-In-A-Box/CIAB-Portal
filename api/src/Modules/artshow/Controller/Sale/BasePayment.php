<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 **/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Container;
use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;
use App\Modules\artshow\Controller\BaseArtshow;
use Atlas\Query\Select;

abstract class BasePayment extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"payment"' => 'type',
    'PaymentID' => 'id',
    'EventID' => 'event',
    'BuyerID' => 'buyer',
    'Date' => 'date',
    'PaymentType' => 'payment_type',
    'Amount' => 'amount',
    'Notes' => 'notes'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('payment', $container);

    }


    /* End BasePayment */
}
