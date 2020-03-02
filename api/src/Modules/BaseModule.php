<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules;

abstract class BaseModule
{

    /**
     * @var object
     */
    protected $source;


    public function __construct($source)
    {
        $this->source = $source;

    }


    /* End BaseModule */
}
