<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $matriz = Company::create([
            'name' => 'Matriz',
            'type' => 'matriz',
            'parent_id' => null,
        ]);

        $filial1 = Company::create([
            'name' => 'Filial 1',
            'type' => 'filial',
            'parent_id' => $matriz->id,
        ]);

        $filial2 = Company::create([
            'name' => 'Filial 2',
            'type' => 'filial',
            'parent_id' => $matriz->id,
        ]);

        User::create([
            'name' => 'Admin Matriz',
            'email' => 'admin@matriz.com',
            'password' => Hash::make('password'),
            'company_id' => $matriz->id,
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Filial 1',
            'email' => 'user@filial1.com',
            'password' => Hash::make('password'),
            'company_id' => $filial1->id,
            'role' => 'employee',
        ]);

        User::create([
            'name' => 'User Filial 2',
            'email' => 'user@filial2.com',
            'password' => Hash::make('password'),
            'company_id' => $filial2->id,
            'role' => 'employee',
        ]);
    }
}

