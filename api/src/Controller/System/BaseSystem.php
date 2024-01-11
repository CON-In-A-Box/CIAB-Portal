<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Tag(
 *      name="administrative",
 *      description="Features around Administration of the site"
 *  )
 *
 *  @OA\Schema(
 *      schema="configuration",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"configuration_entry"}
 *      ),
 *      @OA\Property(
 *          property="field",
 *          type="string",
 *          description="Configuration Field"
 *      ),
 *      @OA\Property(
 *          property="value",
 *          type="string",
 *          description="Configurtion Value"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="configuration_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"configuration_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of configuration fields",
 *          @OA\Items(
 *              ref="#/components/schemas/configuration"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="configuration_not_found",
 *      description="Configuration field not defined.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 */

namespace App\Controller\System;

use Slim\Container;
use App\Controller\BaseController;

abstract class BaseSystem extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('system', $container);

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return ['api.get.log', 'api.get.configuration', 'api.put.log', 'api.put.configuration'];

    }


    /* End BaseSystem */
}
