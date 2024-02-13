<?php declare(strict_types=1);

/**
 * @OA\Delete(
 *     tags={"emails"},
 *     path="/email/{id}",
 *     summary="Delete existing email",
 *     @OA\Parameter(
 *         description="ID of email being deleted",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="OK"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         ref="#/components/responses/400"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/401"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         ref="#/components/responses/email_not_found"
 *     ),
 *     security={{"ciab_auth":{}}}
 * )
 */
namespace App\Controller\Email;

use App\Error\InvalidParameterException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class DeleteEmail extends BaseEmail
{

  
    public function __construct(Container $container)
    {
        parent::__construct($container);

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return null;

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $emailId = $args["id"];
        if ($emailId <= 0) {
            throw new InvalidParameterException("EmailID must be greater than 0");
        }

        $this->emailService->deleteById($emailId);

        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          [null],
          204
        ];

    }


  /* End PutEmail */
}
