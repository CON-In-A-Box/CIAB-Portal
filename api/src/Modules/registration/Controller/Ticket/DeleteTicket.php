<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}",
 *      summary="Deletes a ticket",
 *      @OA\Parameter(
 *          description="Id of the ticket",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/ticket_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class DeleteTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.delete'];
        $this->checkPermissions($permissions);
        $id = $params['id'];
        $sql = "DELETE FROM `Registrations` WHERE RegistrationID = $id";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            throw new NotFoundException('Ticket Not Found');
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteTicket */
}
