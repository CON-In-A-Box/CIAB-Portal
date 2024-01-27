<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Repository\EmailListAccessRepository;

class EmailListAccessService implements ServiceInterface
{

    /**
     * @var EmailListAccessRepository
     */
    protected $emailListAccessRepository;


    public function __construct(EmailListAccessRepository $emailListAccessRepository)
    {
        $this->emailListAccessRepository = $emailListAccessRepository;

    }


    public function post(/*.mixed.*/$data): int
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }


    public function listAll(): array
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }


    public function getById(/*.mixed.*/$id): array
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }

    
    public function put(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        throw new Exception(__CLASS__." Method '__FUNCTION__' not implemented");

    }


    public function deleteByDepartmentId(/*.mixed.*/$departmentId): void
    {
        $this->emailListAccessRepository->deleteByDepartmentId($departmentId);

    }

    
    /* End EmailListAccessService */
}
