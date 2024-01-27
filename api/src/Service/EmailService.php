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
        $this->emailRepository->deleteByDepartmentId($departmentId);

    }

    
    /* End EmailService */
}
