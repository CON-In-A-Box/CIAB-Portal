<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Repository\EmailRepository;

class EmailService implements ServiceInterface
{

    /**
     * @var EmailRepository
     */
    protected $emailRepository;


    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;

    }


    public function post(/*.mixed.*/$data): int
    {
        return $this->emailRepository->insert($data);

    }


    public function listAll(): array
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }


    public function getById(/*.mixed.*/$id): array
    {
        $data = $this->emailRepository->selectById($id);

        $isAlias = 0;
        if ($data["IsAlias"] != null) {
            $isAlias = intval($data["IsAlias"]);
        }

        return [
            "type" => "email",
            "id" => $data["EMailAliasID"],
            "departmentId" => $data["DepartmentID"],
            "email" => $data["EMail"],
            "isAlias" => $isAlias
        ];

    }

    
    public function put(/*.string.*/$id, /*.mixed.*/$data): void
    {
        $this->emailRepository->update($id, $data);

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        $this->emailRepository->deleteById($id);

    }


    public function deleteByDepartmentId(/*.mixed.*/$departmentId): void
    {
        $this->emailRepository->deleteByDepartmentId($departmentId);

    }

    
    /* End EmailService */
}
