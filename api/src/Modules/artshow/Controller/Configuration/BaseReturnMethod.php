<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

abstract class BaseReturnMethod extends BaseConfiguration
{

    protected static $columnsToAttributes = [
    "'return_method'" => 'type',
    'ReturnMethod' => 'method'
    ];

    protected static $table = 'Artshow_ReturnMethod';

    protected static $db_type = 'ReturnMethod';


    /* end BaseReturnMethod */
}
