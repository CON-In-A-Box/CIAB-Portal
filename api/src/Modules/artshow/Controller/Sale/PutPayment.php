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

class PutPayment extends BasePayment
{


    public function __construct(Container $container)
    {
        parent::__construct($container);

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermissions(["api.update.artshow.payment"]);
        $id = $params['id'];

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        Update::new($this->container->db)
            ->table('Artshow_Buyer_Payment')
            ->columns(BasePayment::insertPayloadFromParams($body, false))
            ->whereEquals(['PaymentID' => $id])
            ->perform();

        $target = new GetPayment($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutPayment */
}
