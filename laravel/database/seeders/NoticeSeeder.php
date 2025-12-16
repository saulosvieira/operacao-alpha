<?php

namespace Database\Seeders;

use App\Domain\Career\Models\Career;
use App\Domain\Career\Models\Notice;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $pmsp = Career::where('name', 'Polícia Militar - SP')->first();
        $bombeirosRj = Career::where('name', 'Corpo de Bombeiros - RJ')->first();
        $exercito = Career::where('name', 'Exército Brasileiro')->first();
        $marinha = Career::where('name', 'Marinha do Brasil')->first();
        $fab = Career::where('name', 'Força Aérea Brasileira')->first();

        $notices = [
            // PM-SP
            [
                'career_id' => $pmsp->id,
                'title' => 'Concurso PM-SP 2024 - Soldado 2ª Classe',
                'description' => 'Edital para ingresso na carreira de Soldado PM 2ª Classe da Polícia Militar do Estado de São Paulo. Vagas para ambos os sexos.',
                'publication_date' => now()->subDays(30),
                'active' => true,
            ],
            [
                'career_id' => $pmsp->id,
                'title' => 'Concurso PM-SP 2024 - Oficial',
                'description' => 'Edital para o Curso de Formação de Oficiais (CFO) da Polícia Militar de São Paulo.',
                'publication_date' => now()->subDays(20),
                'active' => true,
            ],
            
            // Bombeiros RJ
            [
                'career_id' => $bombeirosRj->id,
                'title' => 'Concurso CBMERJ 2024 - Soldado',
                'description' => 'Edital para ingresso no Corpo de Bombeiros Militar do Estado do Rio de Janeiro na graduação de Soldado.',
                'publication_date' => now()->subDays(45),
                'active' => true,
            ],
            [
                'career_id' => $bombeirosRj->id,
                'title' => 'Concurso CBMERJ 2024 - Oficial',
                'description' => 'Curso de Formação de Oficiais do Corpo de Bombeiros Militar do Estado do Rio de Janeiro.',
                'publication_date' => now()->subDays(15),
                'active' => true,
            ],
            
            // Exército
            [
                'career_id' => $exercito->id,
                'title' => 'Concurso EsSA 2024',
                'description' => 'Escola de Sargentos das Armas - Formação de Sargentos do Exército Brasileiro.',
                'publication_date' => now()->subDays(25),
                'active' => true,
            ],
            [
                'career_id' => $exercito->id,
                'title' => 'Concurso EsPCEx 2024',
                'description' => 'Escola Preparatória de Cadetes do Exército - Ingresso na carreira de Oficial do Exército.',
                'publication_date' => now()->subDays(10),
                'active' => true,
            ],
            [
                'career_id' => $exercito->id,
                'title' => 'Concurso AMAN 2024',
                'description' => 'Academia Militar das Agulhas Negras - Formação de Oficiais Combatentes do Exército.',
                'publication_date' => now()->subDays(35),
                'active' => true,
            ],
            
            // Marinha
            [
                'career_id' => $marinha->id,
                'title' => 'Concurso Escola Naval 2024',
                'description' => 'Formação de Oficiais da Marinha do Brasil - Corpo da Armada e Corpo de Fuzileiros Navais.',
                'publication_date' => now()->subDays(18),
                'active' => true,
            ],
            [
                'career_id' => $marinha->id,
                'title' => 'Concurso Aprendizes-Marinheiros 2024',
                'description' => 'Ingresso na Marinha do Brasil como Aprendiz-Marinheiro.',
                'publication_date' => now()->subDays(40),
                'active' => true,
            ],
            
            // FAB
            [
                'career_id' => $fab->id,
                'title' => 'Concurso AFA 2024',
                'description' => 'Academia da Força Aérea - Formação de Oficiais Aviadores, Intendentes e de Infantaria.',
                'publication_date' => now()->subDays(22),
                'active' => true,
            ],
            [
                'career_id' => $fab->id,
                'title' => 'Concurso EEAR 2024',
                'description' => 'Escola de Especialistas de Aeronáutica - Formação de Sargentos da FAB.',
                'publication_date' => now()->subDays(28),
                'active' => true,
            ],
        ];

        foreach ($notices as $notice) {
            Notice::create($notice);
        }
    }
}
