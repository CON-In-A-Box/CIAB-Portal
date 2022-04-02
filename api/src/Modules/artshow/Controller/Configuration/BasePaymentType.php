<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

abstract class BasePaymentType extends BaseConfiguration
{

    protected static $columnsToAttributes = [
    "'payment_type'" => 'type',
    'PaymentType' => 'payment'
    ];

    protected static $table = 'Artshow_PaymentType';

    protected static $db_type = 'PaymentType';

    /* end BasePaymentType */
}
