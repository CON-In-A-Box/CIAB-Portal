<?php declare(strict_types=1);

namespace App\Modules\artshow\Controller\Configuration;

abstract class BasePieceType extends BaseConfiguration
{

    protected static $columnsToAttributes = [
    "'piece_type'" => 'type',
    'PieceType' => 'piece'
    ];

    protected static $table = 'Artshow_PieceType';

    protected static $db_type = 'PieceType';

    /* end BasePieceType */
}
