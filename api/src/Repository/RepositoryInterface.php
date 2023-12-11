<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Repository;

interface RepositoryInterface
{


    public function insert(/*.mixed.*/$data): void;


    public function selectAll(): array;


    public function selectById(/*.mixed.*/$id): array;


    public function update(/*.string.*/$id, /*.mixed.*/$data): void;


    public function deleteById(/*.mixed.*/$id): void;


    /* End RepositoryInterface */
}
