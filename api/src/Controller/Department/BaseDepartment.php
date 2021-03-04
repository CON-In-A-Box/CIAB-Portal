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
 *          type="integer",
 *          description="department Id"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="department name"
 *      ),
 *      @OA\Property(
 *          property="division",
 *          type="integer",
 *          description="Division containing this department."
 *      ),
 *      @OA\Property(
 *          property="childCount",
 *          type="integer",
 *          description="Number of child departments"
 *      ),
 *      @OA\Property(
 *          property="fallback",
 *          type="integer",
 *          description="Department that is this departments fallback."
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="array",
 *          description="Department's email addresses.",
 *              @OA\Items(type="string")
 *      )
 *  )
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

abstract class BaseDepartment extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var int
     */
    protected $division = 0;


    public function __construct(Container $container)
    {
        parent::__construct('department', $container);

    }


    protected function buildDepartmentGet($request, $id)
    {
        $path = $request->getUri()->getBaseUrl();
        return ($path.'/department/'.strval($id));

    }


    public function getDepartment($id, $setself = true)
    {
        $output = parent::getDepartment($id);
        if ($setself) {
            if (!empty($output)) {
                if (array_key_exists('id', $output)) {
                    $this->id = $output['id'];
                }
                if (array_key_exists('Division', $output) &&
                    $output['Division'] !== $output['Name']) {
                    $this->division = $this->getDepartment($output['Division'], false)['id'];
                }
            }
        }
        return $output;

    }


    /* End BaseDepartment */
}
