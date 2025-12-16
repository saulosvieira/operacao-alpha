<?php

namespace Database\Seeders;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Enums\AnswerOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    private array $sampleQuestions = [
        [
            'statement' => 'Qual é a capital do Brasil?',
            'options' => [
                'A' => 'São Paulo',
                'B' => 'Rio de Janeiro',
                'C' => 'Brasília',
                'D' => 'Salvador',
                'E' => 'Belo Horizonte',
            ],
            'correct' => 'C',
            'explanation' => 'Brasília é a capital federal do Brasil desde 1960, quando foi inaugurada pelo presidente Juscelino Kubitschek.',
        ],
        [
            'statement' => 'Quem proclamou a República no Brasil?',
            'options' => [
                'A' => 'Dom Pedro I',
                'B' => 'Marechal Deodoro da Fonseca',
                'C' => 'Getúlio Vargas',
                'D' => 'Tiradentes',
                'E' => 'Dom Pedro II',
            ],
            'correct' => 'B',
            'explanation' => 'O Marechal Deodoro da Fonseca proclamou a República em 15 de novembro de 1889.',
        ],
        [
            'statement' => 'Qual é o maior país da América do Sul em extensão territorial?',
            'options' => [
                'A' => 'Argentina',
                'B' => 'Peru',
                'C' => 'Colômbia',
                'D' => 'Brasil',
                'E' => 'Venezuela',
            ],
            'correct' => 'D',
            'explanation' => 'O Brasil é o maior país da América do Sul, ocupando cerca de 47% do território do continente.',
        ],
        [
            'statement' => 'Em que ano ocorreu a independência do Brasil?',
            'options' => [
                'A' => '1500',
                'B' => '1822',
                'C' => '1889',
                'D' => '1808',
                'E' => '1850',
            ],
            'correct' => 'B',
            'explanation' => 'A independência do Brasil foi proclamada por Dom Pedro I em 7 de setembro de 1822.',
        ],
        [
            'statement' => 'Qual é o rio mais extenso do Brasil?',
            'options' => [
                'A' => 'Rio São Francisco',
                'B' => 'Rio Paraná',
                'C' => 'Rio Amazonas',
                'D' => 'Rio Tocantins',
                'E' => 'Rio Paraguai',
            ],
            'correct' => 'C',
            'explanation' => 'O Rio Amazonas é o mais extenso do Brasil e um dos maiores do mundo.',
        ],
        [
            'statement' => 'Quantos estados possui o Brasil?',
            'options' => [
                'A' => '24',
                'B' => '25',
                'C' => '26',
                'D' => '27',
                'E' => '28',
            ],
            'correct' => 'C',
            'explanation' => 'O Brasil possui 26 estados e 1 Distrito Federal, totalizando 27 unidades federativas.',
        ],
        [
            'statement' => 'Qual é a moeda oficial do Brasil?',
            'options' => [
                'A' => 'Peso',
                'B' => 'Dólar',
                'C' => 'Real',
                'D' => 'Cruzeiro',
                'E' => 'Euro',
            ],
            'correct' => 'C',
            'explanation' => 'O Real (R$) é a moeda oficial do Brasil desde 1994.',
        ],
        [
            'statement' => 'Quem foi o primeiro presidente do Brasil?',
            'options' => [
                'A' => 'Getúlio Vargas',
                'B' => 'Marechal Deodoro da Fonseca',
                'C' => 'Floriano Peixoto',
                'D' => 'Prudente de Morais',
                'E' => 'Campos Sales',
            ],
            'correct' => 'B',
            'explanation' => 'Marechal Deodoro da Fonseca foi o primeiro presidente do Brasil (1889-1891).',
        ],
    ];

    public function run(): void
    {
        $exams = Exam::all();

        foreach ($exams as $exam) {
            // Create 30-60 questions per exam
            $numQuestions = rand(30, 60);
            
            for ($i = 1; $i <= $numQuestions; $i++) {
                // Cycle through sample questions
                $sample = $this->sampleQuestions[($i - 1) % count($this->sampleQuestions)];
                
                Question::create([
                    'exam_id' => $exam->id,
                    'question_number' => $i,
                    'statement' => "Questão {$i}: " . $sample['statement'],
                    'statement_image' => null, // Optional images can be added later
                    'option_a' => $sample['options']['A'],
                    'option_a_image' => null,
                    'option_b' => $sample['options']['B'],
                    'option_b_image' => null,
                    'option_c' => $sample['options']['C'],
                    'option_c_image' => null,
                    'option_d' => $sample['options']['D'],
                    'option_d_image' => null,
                    'option_e' => $sample['options']['E'],
                    'option_e_image' => null,
                    'correct_answer' => $sample['correct'],
                    'explanation' => $sample['explanation'],
                ]);
            }
        }
    }
}
