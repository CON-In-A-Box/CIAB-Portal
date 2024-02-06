<?php declare(strict_types=1);

/**
 * @OA\Tag(
 *     name="emails",
 *     description="Features around emails for departments"
 * )
 *
 * @OA\Schema(
 *     schema="email",
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"email"}
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Email ID"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email address"
 *     ),
 *     @OA\Property(
 *         property="departmentId",
 *         type="string",
 *         description="The department ID associated with this email"
 *     ),
 *     @OA\Property(
 *         property="isAlias",
 *         type="integer",
 *         description="Flag indicating whether this email is an alias"
 *     )
 * )
 *
 * @OA\Response(
 *     response="email_not_found",
 *     description="Email not found in the system",
 *     @OA\JsonContent(
 *         ref="#/components/schemas/error"
 *     )
 * )
 */

namespace App\Controller\Email;

use Slim\Container;
use App\Controller\BaseController;
use App\Service\EmailService;

abstract class BaseEmail extends BaseController
{

    /**
     * @var EmailService;
     */
    protected $emailService;

    
    public function __construct(Container $container)
    {
        parent::__construct('email', $container);
        $this->emailService = $container->get("EmailService");

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return null;

    }


  /* End BaseEmail */
}
