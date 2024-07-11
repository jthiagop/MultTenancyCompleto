<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name'      => 'José Thiago',
            'email'     => 'jthiagopereira@gmail.com',
            'password'  => '19931993',
            'avatar'    => 'perfis/1720669911_proneb.png'
        ]);
    }
}
