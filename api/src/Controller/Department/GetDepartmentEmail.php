<?php declare(strict_types=1);

/**
 * @OA\Get(
 *     tags={"departments"},
 *     path="/department/{id}/email",
 *     summary="Gets emails for the department",
 *     @OA\Parameter(
 *         description="The department ID",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Department Emails found",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/email_list"
 *         )
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
 *         ref="#/components/responses/department_not_found"
 *     ),
 *     security={{"ciab_auth":{}}}
 * )
 */
namespace App\Controller\Department;

use App\Error\InvalidParameterException;
use App\Service\DepartmentService;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class GetDepartmentEmail extends BaseDepartment
{

    /**
     * @var DepartmentService
     */
    private $departmentService;


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->departmentService = $container->get("DepartmentService");
        $this->includes = null;

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
        if (!is_numeric($args['id']) || $args['id'] < 0) {
            throw new InvalidParameterException('Department ID must be a number and greater than 0.');
        }

        $result = $this->departmentService->listAllEmails($args['id']);
        return [
          \App\Controller\BaseController::LIST_TYPE,
          $result,
          array('type' => 'email_list')
        ];

    }


    /* End GetDepartmentEmail */
}
