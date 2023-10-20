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


    /**
     * Optional member
     *
     * The static 'install' member will be called first initialization of the system
     *
     * static public function install($container);
     */


    /* End BaseModule */
}
