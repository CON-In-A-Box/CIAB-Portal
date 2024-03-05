<?php declare(strict_types=1);

namespace App\Repository;

use Exception;
use Atlas\Query\Select;

class PermissionRepository implements RepositoryInterface
{

    protected $db;

    
    public function __construct($db)
    {
        $this->db = $db;

    }


    public function insert(/*.mixed.*/$data): int
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function selectAll(): array
    {
        $select = Select::new($this->db);
        $select->columns(
            'PermissionID',
            'Position',
            'Permission',
            'Note'
        )->from('ConComPermissions');

        return $select->fetchAll();

    }


    public function selectById(/*.mixed.*/$id): array
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function update(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    /* End PermissionRepository */
}
