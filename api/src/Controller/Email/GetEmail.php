<?php declare(strict_types=1);

/**
 * @OA\Get(
 *     tags={"emails"},
 *     path="/email/{id}",
 *     summary="Gets an email",
 *     @OA\Parameter(
 *         description="Id of the email",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(
 *             description="Email id",
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email found",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/email"
 *         )
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

use App\Error\NotFoundException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class GetEmail extends BaseEmail
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
        $result = $this->emailService->getById($args['id']);
        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          $result
        ];

    }


  /* End GetEmail */
}
