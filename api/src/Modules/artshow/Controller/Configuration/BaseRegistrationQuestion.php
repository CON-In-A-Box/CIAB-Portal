<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

abstract class BaseRegistrationQuestion extends BaseConfiguration
{

    protected static $columnsToAttributes = [
    "'registration_question'" => 'type',
    'QuestionID' => 'id',
    'BooleanQuestion' => 'boolean',
    'Text' => 'text'
    ];

    protected static $table = 'Artshow_RegistrationQuestion';

    protected static $db_type = 'QuestionID';


    /* end BaseRegistrationQuestion */
}
