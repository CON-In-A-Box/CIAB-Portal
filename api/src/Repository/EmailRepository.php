<?php declare(strict_types=1);

namespace App\Repository;

use App\Error\NotFoundException;
use Exception;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use Atlas\Query\Delete;

class EmailRepository implements RepositoryInterface
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function insert(/*.mixed.*/$data): int
    {
        $insert = Insert::new($this->db);
        $insert->into("EMails")
            ->column('EMail', $data['Email'])
            ->column('DepartmentID', $data['DepartmentID']);

        if (array_key_exists("Alias", $data)) {
            $insert->column('IsAlias', $data['Alias']);
        }
        
        $insert->perform();
        return intval($insert->getLastInsertId());

    }

    
    public function selectById(/*.mixed.*/$id): array
    {
        $select = Select::new($this->db);
        $result = $select->from("EMails")
            ->columns("EMailAliasID", "DepartmentID", "IsAlias", "EMail")
            ->whereEquals(["EMailAliasID" => $id])
            ->fetchOne();

        if (empty($result)) {
            throw new NotFoundException("Email with '$id' Not Found");
        }
          
        return $result;
        
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
        $delete->from("EMails")
            ->whereEquals(["DepartmentID" => $departmentId])
            ->perform();

    }


    /* End EmailRepository */
}
