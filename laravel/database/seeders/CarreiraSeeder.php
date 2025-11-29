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
        $carreiras = [
            [
                'nome' => 'Polícia Federal',
                'descricao' => 'Concursos para Polícia Federal',
                'slug' => 'policia-federal',
                'ativa' => true,
            ],
            [
                'nome' => 'Polícia Rodoviária Federal',
                'descricao' => 'Concursos para PRF',
                'slug' => 'prf',
                'ativa' => true,
            ],
            [
                'nome' => 'Polícia Civil',
                'descricao' => 'Concursos para Polícia Civil',
                'slug' => 'policia-civil',
                'ativa' => true,
            ],
            [
                'nome' => 'Receita Federal',
                'descricao' => 'Concursos para Receita Federal',
                'slug' => 'receita-federal',
                'ativa' => true,
            ],
            [
                'nome' => 'Tribunais',
                'descricao' => 'Concursos para Tribunais (TJ, TRF, TST, etc)',
                'slug' => 'tribunais',
                'ativa' => true,
            ],
        ];

        foreach ($carreiras as $carreira) {
            \App\Models\Carreira::create($carreira);
        }
    }
}
