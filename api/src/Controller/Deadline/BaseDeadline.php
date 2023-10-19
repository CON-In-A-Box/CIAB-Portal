<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="deadlines",
 *      description="Features around event department deadlines"
 *  )
 *
 *  @OA\Schema(
 *      schema="deadline",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"deadline"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Deadline Id"
 *      ),
 *      @OA\Property(
 *          property="deadline",
 *          type="string",
 *          format="date",
 *          description="When this deadline expires"
 *      ),
 *      @OA\Property(
 *          property="department",
 *          description="Department for the deadline",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Department Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="note",
 *          type="string",
 *          description="Note about the deadline."
 *      ),
 *      @OA\Property(
 *          property="scope",
 *          type="integer",
 *          description="The scope of the deadline"
 *      ),
 *      @OA\Property(
 *          property="posted_by",
 *          description="The member who created the deadline",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="deadline_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"deadline_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of deadlines",
 *          @OA\Items(
 *              ref="#/components/schemas/deadline"
 *          ),
 *      )
 *  )
 *
 *  @OA\Response(
 *      response="deadline_not_found",
 *      description="Deadline not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Controller\Deadline;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Controller\BaseController;
use App\Controller\IncludeResource;

abstract class BaseDeadline extends BaseController
{

    use \App\Controller\TraitScope;

    protected static $columnsToAttributes = [
    '"deadline"' => 'type',
    'DeadlineID' => 'id',
    'DepartmentID' => 'department',
    'Deadline' => 'deadline',
    'Note' => 'note',
    'Scope' => 'scope',
    'PostedBy' => 'posted_by'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('deadline', $container);

    }


    public static function install($container): void
    {
        $container->RBAC::customizeRBAC('\App\Controller\Deadline\BaseDeadline::customizeDeadlineRBAC');

    }


    public static function permissions($database): ?array
    {
        $permissions = ['api.get.deadline.all', 'api.get.deadline.staff', 'api.post.deadline.all',
                        'api.put.deadline.all', 'api.delete.deadline.all'];
        $positions = [];
        $values = Select::new($database)
            ->columns('PositionID', 'Name')
            ->from('ConComPositions')
            ->orderBy('`PositionID` ASC')
            ->fetchAll();
        foreach ($values as $value) {
            $positions[intval($value['PositionID'])] = $value['Name'];
        }

        $values = Select::new($database)
            ->columns('DepartmentID')
            ->from('Departments')
            ->fetchAll();
        foreach ($values as $value) {
            $perm_get = 'api.get.deadline.'.$value['DepartmentID'];
            $perm_del = 'api.delete.deadline.'.$value['DepartmentID'];
            $perm_pos = 'api.post.deadline.'.$value['DepartmentID'];
            $perm_put = 'api.put.deadline.'.$value['DepartmentID'];
            $permissions = array_merge($permissions, [$perm_get, $perm_del, $perm_pos, $perm_put]);
        }
        return ($permissions);

    }


    public static function customizeDeadlineRBAC($rbac, $database)
    {
        $positions = [];
        $values = Select::new($database)
            ->columns('PositionID', 'Name')
            ->from('ConComPositions')
            ->orderBy('`PositionID` ASC')
            ->fetchAll();
        foreach ($values as $value) {
            $positions[intval($value['PositionID'])] = $value['Name'];
        }

        $values = Select::new($database)
            ->columns('DepartmentID')
            ->from('Departments')
            ->fetchAll();
        foreach ($values as $value) {
            $perm_get = 'api.get.deadline.'.$value['DepartmentID'];
            $perm_del = 'api.delete.deadline.'.$value['DepartmentID'];
            $perm_pos = 'api.post.deadline.'.$value['DepartmentID'];
            $perm_put = 'api.put.deadline.'.$value['DepartmentID'];
            $target_h = $value['DepartmentID'].'.'.array_keys($positions)[0];
            $target_r = $value['DepartmentID'].'.'.end(array_keys($positions));
            try {
                $rbac->grantPermission($target_h, $perm_del);
                $rbac->grantPermission($target_h, $perm_pos);
                $rbac->grantPermission($target_h, $perm_put);
                $rbac->grantPermission($target_r, $perm_get);
            } catch (Exception\InvalidArgumentException $e) {
                error_log($e);
            }
        }

        try {
            $rbac->grantPermission('all.staff', 'api.get.deadline.staff');
        } catch (Exception\InvalidArgumentException $e) {
            error_log($e);
        }

    }


    /* End BaseDeadline */
}
