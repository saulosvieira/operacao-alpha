<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarreiraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careers = [
            [
                'name' => 'Polícia Militar - SP',
                'slug' => 'policia-militar-sp',
                'description' => 'Carreira militar estadual focada em policiamento ostensivo e preservação da ordem pública no estado de São Paulo.',
                'active' => true,
            ],
            [
                'name' => 'Corpo de Bombeiros - RJ',
                'slug' => 'bombeiros-rj',
                'description' => 'Carreira militar estadual especializada em combate a incêndios, salvamento e atendimento pré-hospitalar no Rio de Janeiro.',
                'active' => true,
            ],
            [
                'name' => 'Exército Brasileiro',
                'slug' => 'exercito',
                'description' => 'Força terrestre das Forças Armadas do Brasil, responsável pela defesa do território nacional.',
                'active' => true,
            ],
            [
                'name' => 'Marinha do Brasil',
                'slug' => 'marinha',
                'description' => 'Força naval das Forças Armadas do Brasil, responsável pela defesa das águas jurisdicionais brasileiras.',
                'active' => true,
            ],
            [
                'name' => 'Força Aérea Brasileira',
                'slug' => 'fab',
                'description' => 'Força aérea das Forças Armadas do Brasil, responsável pela defesa do espaço aéreo nacional.',
                'active' => true,
            ],
        ];

        foreach ($careers as $career) {
            \App\Domain\Career\Models\Career::create($career);
        }
    }
}
