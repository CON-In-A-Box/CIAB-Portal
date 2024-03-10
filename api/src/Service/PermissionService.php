<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Repository\PermissionRepository;

class PermissionService implements ServiceInterface
{

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;


    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;

    }


    public function listAll(): array
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function post(/*.mixed.*/$data): int
    {
        return $this->permissionRepository->insert($data);

    }


    public function getById(/*.mixed.*/$id): array
    {
        $result = $this->permissionRepository->selectById($id);
        return $this->formatOutput($result);

    }


    public function put(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function getByDepartmentId(/*.mixed.*/$departmentId): array
    {
        $allPermissions = $this->permissionRepository->selectAll();

        $filteredResult = [];
        foreach ($allPermissions as $permission) {
            $positionSplit = explode(".", $permission["Position"]);
            if ($positionSplit[0] == "all" || $positionSplit[0] == $departmentId) {
                $filteredResult[] = $this->formatOutput($permission);
            }
        }

        return $filteredResult;

    }


    private function formatOutput($item): array
    {
        $positionSplit = explode(".", $item["Position"]);
        return [
            "type" => "department_permission",
            "id" => intval($item["PermissionID"]),
            "departmentId" => $positionSplit[0],
            "position" => $positionSplit[1],
            "name" => $item["Permission"],
            "note" => $item["Note"]
        ];

    }


    /* End PermissionService */
}
