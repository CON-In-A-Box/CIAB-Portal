<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\System;

use Slim\Container;
use App\Controller\BaseController;

abstract class BaseSystem extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('system', $container);

    }


    /* End BaseSystem */
}
