<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Container;
use App\Controller\BaseController;

require_once __DIR__.'/../../../../functions/users.inc';

abstract class BaseMember extends BaseController
{


    public function __construct(Container $container)
    {
        $this->api_type = 'ciab_member';
        $this->container = $container;

    }


    /* End BaseMember */
}
