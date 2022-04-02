<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;
use App\Controller\PermissionDeniedException;

abstract class PutSale extends BaseSale
{


    public function __construct(Container $container, string $table, string $getter)
    {
        parent::__construct($container);
        $this->table = $table;
        $this->getter = $getter;

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermissions(["api.update.artshow.sale"]);
        $id = $params['id'];

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        Update::new($this->container->db)
            ->table($this->table)
            ->columns(BaseSale::insertPayloadFromParams($body, false))
            ->whereEquals(['SaleID' => $id])
            ->perform();

        $target = new $this->getter($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutSale */
}
