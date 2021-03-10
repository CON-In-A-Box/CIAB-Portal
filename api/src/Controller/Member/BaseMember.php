<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\NotFoundException;

require_once __DIR__.'/../../../../functions/users.inc';

abstract class BaseMember extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('member', $container);

    }


    public function findMember(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        $data = parent::findMember($request, $response, $args, $key, $fields);
        $this->id = $data['id'];
        return $data;

    }


    public function findMemberId(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        $data = parent::findMemberId($request, $response, $args, $key, $fields);
        $this->id = $data['id'];
        return $data;

    }


    /* End BaseMember */
}
