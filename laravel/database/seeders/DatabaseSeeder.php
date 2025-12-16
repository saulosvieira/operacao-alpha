<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Order is important to respect dependencies:
     * 1. Users (no dependencies)
     * 2. Careers (no dependencies)
     * 3. Notices (depends on Careers)
     * 4. Exams (depends on Careers)
     * 5. Questions (depends on Exams)
     * 6. Attempts (depends on Users and Exams)
     * 7. Rankings (depends on Attempts and ExamResults)
     * 8. Approved (no dependencies)
     */
    public function run(): void
    {
        $this->call([
            // Step 1: Create admin user first
            AdminUserSeeder::class,
            
            // Step 2: Create regular users
            UserSeeder::class,
            
            // Step 3: Create careers
            CarreiraSeeder::class,
            
            // Step 4: Create notices (depends on careers)
            NoticeSeeder::class,
            
            // Step 5: Create exams (depends on careers)
            ExamSeeder::class,
            
            // Step 6: Create questions (depends on exams)
            QuestionSeeder::class,
            
            // Step 7: Create attempts and user answers (depends on users, exams, questions)
            AttemptSeeder::class,
            
            // Step 8: Create rankings (depends on attempts)
            RankingSeeder::class,
            
            // Step 9: Create approved list (independent)
            ApprovedSeeder::class,
        ]);
    }
}
