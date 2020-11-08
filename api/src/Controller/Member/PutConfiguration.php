<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class PutConfiguration extends BaseMember
{


    protected static function checkBool($value)
    {
        return (int) filter_var($value, FILTER_VALIDATE_BOOLEAN);

    }


    protected static function checkInt($value)
    {
        return (int) filter_var($value, FILTER_VALIDATE_INTEGER);

    }


    protected function checkSelect($value, $field)
    {
        $sql = "SELECT * FROM `ConfigurationOption` WHERE Field = '$field' AND Name = '$value';";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->fetch() === false) {
            return null;
        }
        return $value;

    }


    protected function verifyValue($value, $field)
    {
        $sql = "SELECT Type FROM `ConfigurationField` WHERE Field = '$field'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        switch ($data[0]['Type']) {
            case 'boolean':
                return PutConfiguration::checkBool($value);
            case 'integer':
                return PutConfiguration::checkInt($value);
            case 'select':
                return $this->checkSelect($value, $field);
            default:
                return $value;
        }

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $accountID = $data['id'];

        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($accountID != $user &&
            !\ciab\RBAC::havePermission("api.put.member")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $body = $request->getParsedBody();
        $field = $body['Field'];
        $value = $this->verifyValue($body['Value'], $field);

        if ($field === null || $value === null) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Value Not Found', 'Value Not Found', 404)];
        }

        $sql = <<<SQL
            INSERT INTO `AccountConfiguration` (AccountID, Field, Value)
            VALUES ($accountID, '$field', '$value')
            ON DUPLICATE KEY UPDATE
                Value = '$value';
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new \App\Controller\Member\GetConfiguration($this->container);

        $args['key'] = $field;
        $data = $target->buildResource($request, $response, $args)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutConfiguration */
}
