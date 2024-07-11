<?php

namespace App\Tenant;

use App\Models\TenantFilial;

class ManagerTenant
{
    public function getTenantIdentity()
    {
        return  auth()->user()->filiais->first()->id;

    }

    public function getTenant(): TenantFilial
    {
        return  auth()->user()->tenant_filials;

    }
}
