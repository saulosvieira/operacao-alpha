<?php

namespace Database\Seeders;

use App\Domain\Approved\Models\Approved;
use App\Domain\Career\Models\Career;
use Illuminate\Database\Seeder;

class ApprovedSeeder extends Seeder
{
    public function run(): void
    {
        $pmsp = Career::where('name', 'Polícia Militar - SP')->first();
        $bombeirosRj = Career::where('name', 'Corpo de Bombeiros - RJ')->first();
        $exercito = Career::where('name', 'Exército Brasileiro')->first();
        $marinha = Career::where('name', 'Marinha do Brasil')->first();
        $fab = Career::where('name', 'Força Aérea Brasileira')->first();

        $approved = [
            // PM-SP
            [
                'career_id' => $pmsp->id,
                'notice_id' => null,
                'name' => 'Carlos Silva Santos',
                'position' => 1,
                'year' => 2023,
            ],
            [
                'career_id' => $pmsp->id,
                'notice_id' => null,
                'name' => 'Ana Paula Oliveira',
                'position' => 3,
                'year' => 2023,
            ],
            [
                'career_id' => $pmsp->id,
                'notice_id' => null,
                'name' => 'Roberto Almeida',
                'position' => 15,
                'year' => 2023,
            ],
            
            // Bombeiros RJ
            [
                'career_id' => $bombeirosRj->id,
                'notice_id' => null,
                'name' => 'Juliana Costa',
                'position' => 2,
                'year' => 2023,
            ],
            [
                'career_id' => $bombeirosRj->id,
                'notice_id' => null,
                'name' => 'Fernando Rodrigues',
                'position' => 8,
                'year' => 2023,
            ],
            
            // Exército
            [
                'career_id' => $exercito->id,
                'notice_id' => null,
                'name' => 'Marcos Vieira',
                'position' => 12,
                'year' => 2023,
            ],
            [
                'career_id' => $exercito->id,
                'notice_id' => null,
                'name' => 'Patricia Lima',
                'position' => 25,
                'year' => 2023,
            ],
            [
                'career_id' => $exercito->id,
                'notice_id' => null,
                'name' => 'Lucas Ferreira',
                'position' => 5,
                'year' => 2023,
            ],
            
            // Marinha
            [
                'career_id' => $marinha->id,
                'notice_id' => null,
                'name' => 'Camila Souza',
                'position' => 18,
                'year' => 2023,
            ],
            [
                'career_id' => $marinha->id,
                'notice_id' => null,
                'name' => 'Rafael Martins',
                'position' => 42,
                'year' => 2023,
            ],
            
            // FAB
            [
                'career_id' => $fab->id,
                'notice_id' => null,
                'name' => 'Gabriela Mendes',
                'position' => 7,
                'year' => 2023,
            ],
            [
                'career_id' => $fab->id,
                'notice_id' => null,
                'name' => 'Bruno Carvalho',
                'position' => 22,
                'year' => 2023,
            ],
            [
                'career_id' => $fab->id,
                'notice_id' => null,
                'name' => 'Thiago Pereira',
                'position' => 3,
                'year' => 2023,
            ],
            
            // Additional approved candidates
            [
                'career_id' => $pmsp->id,
                'notice_id' => null,
                'name' => 'Mariana Ribeiro',
                'position' => 5,
                'year' => 2023,
            ],
            [
                'career_id' => $bombeirosRj->id,
                'notice_id' => null,
                'name' => 'Diego Santos',
                'position' => 4,
                'year' => 2023,
            ],
        ];

        foreach ($approved as $person) {
            Approved::create($person);
        }
    }
}
