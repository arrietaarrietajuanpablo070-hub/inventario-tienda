<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Administrador', 'email' => 'admin@tienda.com', 'password' => 'admin123', 'rol' => 'admin'],
            ['name' => 'Cajero', 'email' => 'cajero@tienda.com', 'password' => 'cajero123', 'rol' => 'cajero'],
        ];

        foreach ($usuarios as $datos) {
            $usuario = User::firstOrNew(['email' => $datos['email']]);
            $usuario->forceFill([
                'name' => $datos['name'],
                'password' => Hash::make($datos['password']),
                'rol' => $datos['rol'],
            ])->save();
        }
    }
}
