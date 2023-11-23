<?php declare(strict_types=1);
namespace App\Controller;

trait TraitScope
{


    protected function filterScope(&$data)
    {
        foreach ($data as $index => $target) {
            if ($target['scope'] === null) {
                $data[$index]['scope'] = 2;
            }
        }
        if (!$this->container->RBAC->havePermission("api.get.{$this->api_type}.all")) {
            foreach ($data as $index => $target) {
                if ($target['scope'] >= 2) {
                    if (!$this->container->RBAC->havePermission("api.get.{$this->api_type}.{$target['department']}")) {
                        unset($data[$index]);
                    }
                } elseif ($target['scope'] == 1) {
                    if (!$this->container->RBAC->havePermission("api.get.{$this->api_type}.staff")) {
                        unset($data[$index]);
                    }
                }
            }
        }

        return $data;

    }


    protected function verifyScope(&$data)
    {
        if ($data['scope'] === null) {
            $data['scope'] = 2;
        }
        if ($data['scope'] >= 2) {
            $permissions = [
            "api.get.{$this->api_type}.all",
            "api.get.{$this->api_type}.{$data['department']}"
            ];
            $this->checkPermissions($permissions);
        } elseif ($data['scope'] == 1) {
            $permissions = [
            "api.get.{$this->api_type}.all",
            "api.get.{$this->api_type}.staff"];
            $this->checkPermissions($permissions);
        }

    }


/* End */
}
