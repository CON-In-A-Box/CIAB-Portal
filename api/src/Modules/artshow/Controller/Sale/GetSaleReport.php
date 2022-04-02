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

class GetSaleReport extends BaseSale
{
    use \App\Controller\Stream\StreamController;


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'buyer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        new IncludeResource('\App\Modules\artshow\Controller\Art\GetArt', 'piece', 'piece')
        ];

    }


    public function doWork(Request $request, Response $response, $args, $lastEventId): void
    {
        $eid = $this->getEventId($request);
        do {
            $data = Select::new($this->container->db)
                ->columns(...BaseSale::selectMapping())
                ->from('Artshow_Art_Sale')
                ->whereEquals(['EventID' => $eid])
                ->orderBy('SaleID')
                ->perPage(1)
                ->page($lastEventId)
                ->fetchAll();

            if (empty($data)) {
                break;
            }

            $result = json_encode($this->expandIncludes($request, $response, $args, $data));
            $this->sendStreamPacket($lastEventId, $result);
            $lastEventId += 1;
        } while (true);

        $this->endStream();

    }


    /* end GetSaleReport */
}
