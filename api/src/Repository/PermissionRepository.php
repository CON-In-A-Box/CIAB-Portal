<?php declare(strict_types=1);

namespace App\Repository;

use Exception;
use Atlas\Query\Select;
use Atlas\Query\Insert;
use Atlas\Query\Delete;

class PermissionRepository implements RepositoryInterface
{

    protected $db;

    
    public function __construct($db)
    {
        $this->db = $db;

    }


    public function insert(/*.mixed.*/$data): int
    {
        $insert = Insert::new($this->db);
        $insert->into("ConComPermissions")
            ->column("Position", $data["Position"])
            ->column("Permission", $data["Permission"]);
        
        $insert->perform();
        return intval($insert->getLastInsertId());
        
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
        $select = Select::new($this->db);
        $select->columns(
            'PermissionID',
            'Position',
            'Permission',
            'Note'
        )->from('ConComPermissions')
            ->whereEquals(["PermissionID" => $id]);

        return $select->fetchOne();

    }


    public function update(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        $delete = Delete::new($this->db);
        $delete->from('ConComPermissions')
            ->whereEquals(["PermissionID" => $id])
            ->perform();

    }


    /* End PermissionRepository */
}
