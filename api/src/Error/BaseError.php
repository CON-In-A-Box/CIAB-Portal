<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="error",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"error"}
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          description="Error ID"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          description="Error Status"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Error Message"
 *      )
 *  )
 **/

namespace App\Error;
