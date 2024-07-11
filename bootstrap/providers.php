<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\GlobalVariablesServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // Tenancy
    Spatie\Permission\PermissionServiceProvider::class,
    App\Providers\GlobalVariablesServiceProvider::class, // Avatar de Perfil
];
