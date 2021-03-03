<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;

class ModuleStaff extends BaseModule
{


    public function __construct($source)
    {
        parent::__construct($source);

    }


    public function valid()
    {
        if ($this->source !== null) {
            if (get_class($this->source) === 'App\Controller\Member\GetMember' ||
                get_class($this->source) === 'App\Controller\Department\GetDepartment') {
                return true;
            }
        }
        return false;

    }


    public function handle(Request $request, Response $response, $data, $code, $container)
    {
        if (get_class($this->source) == 'App\Controller\Member\GetMember') {
            if (array_key_exists('id', $data)) {
                $id = $data['id'];
                $event = \current_eventID();
                $sql = <<<SQL
            SELECT
                COUNT(ListRecordID) AS c
            FROM
                `ConComList`
            WHERE
                AccountID = $id
                AND EventID = $event
                AND DepartmentID NOT IN (
                    SELECT
                        `DepartmentID`
                    FROM
                        `Departments`
                    WHERE
                        Name = 'Historical Placeholder'
                )
                AND DepartmentID NOT IN (
                    SELECT
                        `DepartmentID`
                    FROM
                        `Departments`
                    WHERE
                        ParentDepartmentID IN (
                            SELECT
                                `DepartmentID`
                            FROM
                                `Departments`
                            WHERE
                                Name = 'Historical Placeholder'
                        )
                )
SQL;
                $sth = $container->db->prepare($sql);
                $sth->execute();
                $value = $sth->fetch();
                if (intval($value['c']) > 0) {
                    $this->source->addHateoasLink('staff', 'member/'.$id.'/staff_membership', 'GET');
                }
            }
        }
        if (get_class($this->source) == 'App\Controller\Department\GetDepartment') {
            if (array_key_exists('id', $data)) {
                $id = $data['id'];
                $this->source->addHateoasLink('staff', 'department/'.$id.'/staff', 'GET');
            }
        }
        return $data;

    }


    /* End ModuleStaff */
}
