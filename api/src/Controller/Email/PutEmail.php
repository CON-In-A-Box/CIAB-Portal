<?php declare(strict_types=1);

/**
 * @OA\Put(
 *     tags={"emails"},
 *     path="/email/{id}",
 *     summary="Update existing email",
 *     @OA\Parameter(
 *         description="ID of email being updated",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="Email",
 *                     description="The email address for the department",
 *                     nullable=true,
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="DepartmentID",
 *                     description="The ID for the department",
 *                     nullable=true,
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="IsAlias",
 *                     description="Indicates whether this email address is an alias",
 *                     nullable=true,
 *                     type="integer"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/email"
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/401"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         ref="#/components/responses/400"
 *     ),
 *     security={{"ciab_auth":{}}}
 * )
 */
namespace App\Controller\Email;

use App\Error\InvalidParameterException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class PutEmail extends BaseEmail
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

        $body = $this->validateRequestParams($request);

        $this->emailService->put($emailId, $body);
        $result = $this->emailService->getById($emailId);

        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          $result,
          200
        ];

    }


    private function validateRequestParams(Request $request): array
    {
        $body = $request->getParsedBody();

        
        if ($body == null || count($body) == 0) {
            throw new InvalidParameterException("Must include at least one of 'Email', 'DepartmentID', or 'IsAlias'");
        }

        $hasOneValidField = array_key_exists('Email', $body) || array_key_exists('DepartmentID', $body) || array_key_exists('IsAlias', $body);
        if (!$hasOneValidField) {
            throw new InvalidParameterException("Must include at least one of 'Email', 'DepartmentID', or 'IsAlias'");
        }

        if (array_key_exists('Email', $body) && strlen(trim($body['Email'])) == 0) {
            throw new InvalidParameterException("Email must be a non-empty string");
        }

        if (array_key_exists('DepartmentID', $body) && (!is_numeric($body['DepartmentID']) || $body['DepartmentID'] <= 0)) {
            throw new InvalidParameterException("DepartmentID must be greater than 0");
        }

        if (array_key_exists("IsAlias", $body) && (!is_numeric($body['IsAlias']) || ($body['IsAlias'] != 0 && $body['IsAlias'] != 1))) {
            throw new InvalidParameterException("IsAlias must be 0 or 1");
        }

        return $body;

    }


  /* End PutEmail */
}
