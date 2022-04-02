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

abstract class DeleteSale extends BaseSale
{


    public function __construct(Container $container, string $table)
    {
        parent::__construct($container);
        $this->table = $table

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $result = Select::new($this->container->db)
            ->columns('*')
            ->from($this->table)
            ->whereEquals(['SaleID' => $params['id']])
            ->fetchAll();
        if (empty($result)) {
            throw new NotFoundException('Artshow Sale Not Found');
        }
        $target = $result[0];
        $this->checkSalePermission($request, $response, 'delete', $target['SaleID']);

        Delete::new($this->container->db)
            ->from($this->table)
            ->whereEquals(['SaleID' => $target['SaleID']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteSale */
}
