<?php declare(strict_types=1);

namespace App\Repository;

use Exception;
use Atlas\Query\Delete;

class EmailListAccessRepository implements RepositoryInterface
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function insert(/*.mixed.*/$data): int
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }

    
    public function selectById(/*.mixed.*/$id): array
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");
          
    }


    public function selectAll(): array
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");
          
    }


    public function update(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");
          
    }


    public function deleteById(/*.mixed.*/$id): void
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");
          
    }


    public function deleteByDepartmentId(/*.mixed.*/$departmentId): void
    {
        $delete = Delete::new($this->db);
        $delete->from("EmailListAccess")
            ->whereEquals(["DepartmentID" => $departmentId])
            ->perform();

    }


  /* End EmailListAccessRepository */
}
