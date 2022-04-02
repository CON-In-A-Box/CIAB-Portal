<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetPayment extends BasePayment
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
        $data = Select::new($this->container->db)
            ->columns(...BasePayment::selectMapping())
            ->from('Artshow_Buyer_Payment')
            ->whereEquals(['PaymentID' => $params['id']])
            ->fetchOne();
        if (empty($data)) {
            throw new NotFoundException('Payment Not Found');
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end GetPayment */
}
