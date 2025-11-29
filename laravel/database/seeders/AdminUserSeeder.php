<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@simulados.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addYear(),
            ]
        );

        // Usuário de teste assinante
        \App\Models\User::updateOrCreate(
            ['email' => 'usuario@teste.com'],
            [
                'name' => 'Usuário Teste',
                'password' => bcrypt('teste123'),
                'role' => 'user',
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addMonths(3),
            ]
        );

        // Usuário de teste não assinante
        \App\Models\User::updateOrCreate(
            ['email' => 'gratuito@teste.com'],
            [
                'name' => 'Usuário Gratuito',
                'password' => bcrypt('teste123'),
                'role' => 'user',
                'subscription_status' => 'inactive',
            ]
        );
    }
}
