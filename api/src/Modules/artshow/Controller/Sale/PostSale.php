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

abstract class PostSale extends BaseSale
{


    public function __construct($container, $table, $getter)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'buyer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];
        $this->table = $table;
        $this->getter = $getter;

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermissions(["api.post.artshow.sale"]);
        $eid = $this->getEventId($request);

        $fields = array();
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $body = $request->getParsedBody();

        if (!array_key_exists('event', $body)) {
            $body['event'] = $eid;
        }

        $quantity = 1;
        if (array_key_exists('quantity', $body)) {
            $quantity = intval($body['quantity']);
        }

        $data = [];
        for ($i = 0; $i < $quantity; $i ++) {
            $insert = Insert::new($this->container->db)
                ->into($this->table)
                ->columns(BaseSale::insertPayloadFromParams($body));
            $insert->perform();
            $id = $insert->getLastInsertId();
            $target = new $this->getter($this->container);
            $data[] = $target->buildResource($request, $response, ['id' => $id])[1];
        }

        $output = array();
        $output['type'] = 'artshow_sale_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output,
        201
        ];

    }


    /* end PostSale */
}
