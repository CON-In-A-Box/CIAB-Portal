<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="cycles",
 *      description="Features around annual cycles"
 *  )
 *
 *  @OA\Schema(
 *      schema="cycle",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"cycle"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="cycle Id"
 *      ),
 *      @OA\Property(
 *          property="date_from",
 *          type="string",
 *          format="date",
 *          description="cycle start date"
 *      ),
 *      @OA\Property(
 *          property="date_to",
 *          type="string",
 *          format="date",
 *          description="cycle ending date"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="cycle_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"cycle_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of cycles",
 *          @OA\Items(
 *              ref="#/components/schemas/cycle"
 *          ),
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="cycle_not_found",
 *      description="Cycle not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Controller\Cycle;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

abstract class BaseCycle extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;

    protected static $columnsToAttributes = [
    '"cycle"' => 'type',
    'AnnualCycleID' => 'id',
    'DateFrom' => 'date_from',
    'DateTo' => 'date_to',
    ];


    public function __construct(Container $container)
    {
        parent::__construct('cycle', $container);

    }


    protected function getCycle(Request $request, Response $response, $params)
    {
        $cycle = new GetCycle($this->container);
        $cycle->buildResource($request, $response, $params);
        return $cycle;

    }


    /* End BaseCycle */
}
