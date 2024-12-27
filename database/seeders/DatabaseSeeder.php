<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Caminho original da imagem
        $originalPath = resource_path('/assets/midia/avatars/1avatar.png');

        // Caminho para onde a imagem será copiada
        $storagePath = '/public/avatars/1avatar.png';

        // Copiar a imagem para o diretório de armazenamento
        Storage::copy($originalPath, $storagePath);

        User::factory()->create([
            'name'      => 'José Thiago',
            'email'     => 'jthiagopereira@gmail.com',
            'password'  => '19931993',
            'avatar' => $storagePath,        ]);
    }
}
