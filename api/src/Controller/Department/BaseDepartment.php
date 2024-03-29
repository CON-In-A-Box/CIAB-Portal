<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="departments",
 *      description="Features around staffing departments for events"
 *  )
 *
 *  @OA\Schema(
 *      schema="department",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"department"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="string",
 *          description="department Id"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="department name"
 *      ),
 *      @OA\Property(
 *          property="parent",
 *          description="Department that is the parent of this department.",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              ),
 *              @OA\Schema(
 *                  type="string",
 *                  description="Department Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="child_count",
 *          type="string",
 *          description="Number of child departments"
 *      ),
 *      @OA\Property(
 *          property="fallback",
 *          description="Department that is this departments fallback.",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              ),
 *              @OA\Schema(
 *                  type="string",
 *                  description="Department Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="array",
 *          description="Department's email addresses.",
 *              @OA\Items(type="string")
 *      )
 *  )
 *
 *   @OA\Schema(
 *      schema="department_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"department_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of departments",
 *          @OA\Items(
 *              ref="#/components/schemas/department"
 *          ),
 *      )
 *  )
 *
 *   @OA\Schema(
 *     schema="department_permission",
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"department_permission"}
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The permission ID"
 *     ),
 *     @OA\Property(
 *         property="departmentId",
 *         type="string",
 *         description="The department ID"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="string",
 *         description="The department position"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the permission"
 *     ),
 *     @OA\Property(
 *         property="note",
 *         type="string",
 *         description="The note associated with this permission",
 *         nullable=true
 *     )
 *   )
 *
 *   @OA\Schema(
 *     schema="department_permission_list",
 *     allOf = {
 *         @OA\Schema(ref="#/components/schemas/resource_list")
 *     },
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"department_permission_list"}
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         description="List of department permissions",
 *         @OA\Items(
 *             ref="#/components/schemas/department_permission"
 *         )
 *     )
 *   )
 *
 *   @OA\Response(
 *      response="department_not_found",
 *      description="Department not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 */

namespace App\Controller\Department;

use Slim\Container;
use Slim\Http\Request;
use App\Controller\BaseController;
use App\Controller\IncludeResource;

abstract class BaseDepartment extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('department', $container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'fallback'),
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'parent')
        ];

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return null;

    }


    public function getDepartment($id, $setself = true)
    {
        $output = parent::getDepartment($id);
        if ($setself) {
            if (array_key_exists('id', $output)) {
                $this->id = $output['id'];
            }
        }
        return $output;

    }


    /* End BaseDepartment */
}
