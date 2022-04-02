<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\PermissionDeniedException;
use Atlas\Query\Insert;

class PostPayment extends BasePayment
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'buyer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermissions(["api.post.artshow.payment"]);
        $eid = $this->getEventId($request);

        $fields = array();
        $body = $request->getParsedBody();

        if (!array_key_exists('event', $body)) {
            $body['event'] = $eid;
        }

        $insert = Insert::new($this->container->db)
            ->into('Artshow_Buyer_Payment')
            ->columns(BasePayment::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetPayment($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostPayment */
}
