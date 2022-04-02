<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

abstract class BasePriceType extends BaseConfiguration
{

    protected static $columnsToAttributes = [
    "'price_type'" => 'type',
    'PriceType' => 'price',
    'Position' => 'position',
    'SetPrice' => 'artist_set',
    'Fixed' => 'fixed'
    ];

    protected static $table = 'Artshow_PriceType';

    protected static $db_type = 'PriceType';


    /* end BasePriceType */
}
