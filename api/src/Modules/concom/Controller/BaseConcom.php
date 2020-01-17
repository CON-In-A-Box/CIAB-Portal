<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Container;
use Slim\Http\Request;
use App\Controller\BaseController;

abstract class BaseConcom extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('concom', $container);

    }


    protected function buildEntry(Request $request, $dept, $member, $note, $position)
    {
        $path = $request->getUri()->getBaseUrl();
        return ([
                'type' => 'concom_entry',
                'memberId' => $member,
                'note' => $note,
                'position' => $position,
                'departmentId' => $dept,
                'links' => array([
                    'method' => 'member',
                    'href' => $path.'/member/'.$member,
                    'request' => 'GET'
                    ],
                    [
                    'method' => 'department',
                    'href' => $path.'/department/'.$dept,
                    'request' => 'GET'
                    ]
            )
        ]);

    }


    /* End BaseConcom */
}
