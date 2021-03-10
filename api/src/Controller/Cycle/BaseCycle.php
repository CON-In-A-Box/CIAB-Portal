<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Cycle;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\NotFoundException;

abstract class BaseCycle extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('cycle', $container);

    }


    protected function getCycle($params)
    {
        $sth = $this->container->db->prepare("SELECT * FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$params['id']);
        $sth->execute();
        $cycles = $sth->fetchAll();
        if (empty($cycles)) {
            throw new NotFoundException('Cycle Not Found');
        }

        return $cycles;

    }


    /* End BaseCycle */
}
