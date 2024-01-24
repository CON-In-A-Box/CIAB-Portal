<?php declare(strict_types=1);

namespace App\Repository;

use Atlas\Query\Select;
use Atlas\Query\Insert;
use Atlas\Query\Update;

class DepartmentRepository implements RepositoryInterface
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function insert(/*.mixed.*/$data): int
    {
        $insert = Insert::new($this->db);
        $insert->into('Departments')
            ->column('Name', $data["Name"]);

        if (array_key_exists("ParentID", $data)) {
            $insert->column('ParentDepartmentID', $data["ParentID"]);
        } else {
            $minDeptId = Select::new($this->db)
                ->from("Departments")
                ->columns("MIN(DepartmentID) as DepartmentID")
                ->fetchOne();
            $insert->column('ParentDepartmentID', $minDeptId["DepartmentID"]);
        }

        if (array_key_exists("FallbackID", $data)) {
            $insert->column('FallbackID', $data["FallbackID"]);
        } else {
            $insert->set('FallbackID', null);
        }

        $insert->perform();
        $lastInsertId = $insert->getLastInsertId();

        // If there was no parent, then the department is also the parent.
        if (!array_key_exists("ParentID", $data)) {
            $parentUpdate = Update::new($this->db);
            $parentUpdate->table('Departments')
                ->column('ParentDepartmentID', $lastInsertId)
                ->whereEquals(['DepartmentID' => $lastInsertId])
                ->perform();
        }

        return intval($lastInsertId);
        
    }


    public function selectAll(): array
    {
        $select = $this->getDepartmentLookupSelect();
        $historicalPlaceholders = $select->subSelect()->columns('DepartmentID')->from('Departments')->where('Name = "Historical Placeholder"');

        $select->where('depts.DepartmentID NOT IN ', $historicalPlaceholders)
            ->where('depts.ParentDepartmentID NOT IN ', $historicalPlaceholders);

        return $select->fetchAll();

    }


    public function selectById(/*.mixed.*/$departmentId): array
    {
        $select = $this->getDepartmentLookupSelect();
        $select->where('depts.Name != "Historical Placeholder"');

        if (is_array($departmentId)) {
            $select->andWhere('(depts.DepartmentID IN ', $departmentId);
        } else {
            $select->andWhere('(depts.DepartmentID = ', $departmentId);
        }
        $select->catWhere(' OR depts.ParentDepartmentID = ', $departmentId)
            ->catWhere(')');

        return $select->fetchAll();

    }


    public function update(/*.string.*/$id, /*.mixed.*/$data): void
    {
        $update = Update::new($this->db);
        $update->table('Departments');

        if (array_key_exists("Name", $data)) {
            $update->column("Name", $data["Name"]);
        }

        // Fallback
        if (array_key_exists("FallbackID", $data)) {
            $update->column("FallbackID", $data["FallbackID"]);
        }

        if (array_key_exists("ParentID", $data)) {
            $update->column("ParentDepartmentID", $data["ParentID"]);
        }

        $update->whereEquals(["DepartmentID" => $id])
            ->perform();
        
    }


    public function deleteById(/*.mixed.*/$id): void
    {
        $placeholderDeptId = Select::new($this->db)
            ->columns("DepartmentID")
            ->from("Departments")
            ->where("Name = 'Historical Placeholder'")
            ->fetchOne();
        
        $update = Update::new($this->db);
        $update->table("Departments")
            ->column("FallbackID", null)
            ->column("ParentDepartmentID", $placeholderDeptId["DepartmentID"])
            ->whereEquals(["DepartmentID" => $id])
            ->perform();

        $update = Update::new($this->db);
        $update->table("Departments")
            ->column("FallbackID", null)
            ->whereEquals(["FallbackID" => $id])
            ->perform();

    }


    private function getDepartmentLookupSelect(): Select
    {
        $select = Select::new($this->db);
        $select->columns(
            'depts.DepartmentID',
            'depts.Name',
            'parentDepts.DepartmentID as ParentID',
            'parentDepts.Name as ParentName',
            'fallbackDepts.DepartmentID as FallbackID',
            'fallbackDepts.Name as FallbackName'
        )
            ->columns(
                $select->subSelect()
                    ->columns('COUNT(DepartmentID)')
                    ->from('Departments as childDepts')
                    ->where('childDepts.ParentDepartmentID = depts.DepartmentID')
                    ->andWhere('Name != "Historical Placeholder"')
                    ->as('ChildCount')
                    ->getStatement()
            )
            ->columns(
                $select->subSelect()
                    ->columns('GROUP_CONCAT(Email)')
                    ->from('EMails')
                    ->where('DepartmentID = depts.DepartmentID')
                    ->as('Email')
                    ->getStatement()
            )
            ->from('Departments as depts')
            ->join('LEFT', 'Departments as parentDepts', 'parentDepts.DepartmentID = depts.ParentDepartmentID')
            ->join('LEFT', 'Departments as fallbackDepts', 'fallbackDepts.DepartmentID = depts.FallbackID');

        return $select;

    }


    /* End DepartmentRepository */
}
