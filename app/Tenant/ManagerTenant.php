<?php

namespace App\Tenant;

use App\Models\TenantFilial;

class ManagerTenant
{
    public function getTenantIdentity()
    {
        if (!auth()->check()) {
            return null;
        }
        
        return auth()->user()->filiais->first()->id;
    }

    public function getTenant(): ?TenantFilial
    {
        if (!auth()->check()) {
            return null;
        }
        
        return auth()->user()->tenant_filials;
    }
}
