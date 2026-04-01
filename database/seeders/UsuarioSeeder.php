<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario; 
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        Usuario::create([
            'nombre' => 'Admin Test',
            'correo' => 'admin@test.com',
            'contra' => Hash::make('123456'), 
        ]);
        
        $this->command->info('Usuario de prueba creado correctamente.');
    }
}