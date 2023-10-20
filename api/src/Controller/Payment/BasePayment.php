<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="payments",
 *      description="Features around payments"
 *  )
 *
 *  @OA\Schema(
 *      schema="payment",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"payment"}
 *      ),
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="payment_not_found",
 *      description="Payment Processor not found.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Controller\Payment;

use Slim\Container;
use App\Controller\BaseVendorController;

abstract class BasePayment extends BaseVendorController
{


    public function __construct(Container $container)
    {
        parent::__construct('payment', $container);

    }


    public static function baseInstall($database): void
    {

    }


    public static function basePermissions($database): ?array
    {
        return null;

    }


    /* End BasePayment */
}
