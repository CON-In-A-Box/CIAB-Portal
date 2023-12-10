<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Service;

interface ServiceInterface
{


    public function post(/*.mixed.*/$data): void;


    public function listAll(): array;


    public function getById(/*.mixed.*/$id): array;


    public function put(/*.string.*/$id, /*.mixed.*/$data): void;


    public function deleteById(/*.mixed.*/$id): void;


    /* End ServiceInterface */
}
