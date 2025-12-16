<?php

namespace Database\Seeders;

use App\Domain\Career\Models\Career;
use App\Domain\Exam\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $careers = Career::all();

        foreach ($careers as $career) {
            // Create 3-5 exams per career
            $numExams = rand(3, 5);
            
            for ($i = 1; $i <= $numExams; $i++) {
                Exam::create([
                    'career_id' => $career->id,
                    'title' => "Simulado {$career->name} - Prova {$i}",
                    'description' => "Simulado completo para {$career->name} baseado em provas anteriores. Contém questões de conhecimentos gerais e específicos da carreira.",
                    'time_limit_minutes' => rand(120, 240), // 2 to 4 hours
                    'active' => $i <= 3, // First 3 are active
                ]);
            }
        }
    }
}
