<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Delete;

class DeletePayment extends BasePayment
{


    public function __construct(Container $container)
    {
        parent::__construct($container);

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $result = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_Buyer_Payment')
            ->whereEquals(['PaymentID' => $params['id']])
            ->fetchAll();
        if (empty($result)) {
            throw new NotFoundException('Artshow Payment Not Found');
        }
        $target = $result[0];
        $this->checkPaymentPermission($request, $response, 'delete', $target['PaymentID']);

        Delete::new($this->container->db)
            ->from('Artshow_Buyer_Payment')
            ->whereEquals(['PaymentID' => $target['PaymentID']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeletePayment */
}
