<?php declare(strict_types=1);

/**
 *  @OA\Tag(
 *      name="stores",
 *      description="Features around stores"
 *  )
 *
 *  @OA\Schema(
 *      schema="store",
 *      deprecated=true,
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"store"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="string",
 *          description="store Id"
 *      ),
 *      @OA\Property(
 *          property="store_slug",
 *          type="string",
 *          description="short, unique name for store"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="public-facing name for store, e.g. 'Membership'"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          type="string",
 *          description="description of store, eventually public facing"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="store_list",
 *      deprecated=true,
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"store_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of stores",
 *          @OA\Items(
 *              ref="#/components/schemas/store"
 *          )
 *      )
 *  )
 *
 *  @OA\Response(
 *      response="store_not_found",
 *      description="Store not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *  )
 */

namespace App\Controller\Stores;

use Atlas\Query\Select;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\BaseController;
use App\Error\NotFoundException;

abstract class BaseStore extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;

    /* This the various mapping functions here are intended as a
     * prototype for how we could handle name conversion. It's
     * the beginning of an idea, not the end of it.
     */
    protected static $columnsToAttributes = [
    'StoreID' => 'id',
    'StoreSlug' => 'store_slug',
    'Name' => 'name',
    'Description' => 'description'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('store', $container);

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return null;

    }


    protected function getStore(array $params, Request $request, Response $response, &$error)
    {
        $select = Select::new($this->container->db);
        $select->columns(...$this->selectMapping());
        $select->from('Stores');
        $store = $select->whereEquals(['StoreID' => $params['id']])->fetchOne();

        if (empty($store)) {
            throw new NotFoundException("Could not find Store ID ${params['id']}");
        }

        return $store;

    }


    /* End BaseStores */
}
