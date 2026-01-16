<?php

namespace Tests\Unit\Import;

use Tests\TestCase;
use App\Domain\Import\Services\DuplicateDetectionService;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use App\Domain\Career\Models\Career;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DuplicateDetectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private DuplicateDetectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DuplicateDetectionService();
    }

    /** @test */
    public function it_detects_exact_duplicate_questions()
    {
        // Create a career and exam
        $career = Career::create([
            'name' => 'Test Career',
            'slug' => 'test-career',
            'description' => 'Test career description',
        ]);

        $exam = Exam::create([
            'career_id' => $career->id,
            'title' => 'Test Exam',
            'description' => 'Test exam description',
            'time_limit_minutes' => 60,
            'active' => true,
        ]);

        // Create an existing question
        $existingQuestion = Question::create([
            'exam_id' => $exam->id,
            'question_number' => 1,
            'statement' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'B',
        ]);

        // Check for duplicate with exact same data
        $questionData = [
            'statement' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'B',
        ];

        $result = $this->service->checkForDuplicate($exam, $questionData);

        $this->assertTrue($result['is_duplicate']);
        $this->assertEquals('exact', $result['type']);
        $this->assertEquals($existingQuestion->id, $result['duplicate_question_id']);
        $this->assertEquals(1.0, $result['similarity_score']);
    }

    /** @test */
    public function it_detects_similar_duplicate_questions()
    {
        // Create a career and exam
        $career = Career::create([
            'name' => 'Test Career',
            'slug' => 'test-career',
            'description' => 'Test career description',
        ]);

        $exam = Exam::create([
            'career_id' => $career->id,
            'title' => 'Test Exam',
            'description' => 'Test exam description',
            'time_limit_minutes' => 60,
            'active' => true,
        ]);

        // Create an existing question
        $existingQuestion = Question::create([
            'exam_id' => $exam->id,
            'question_number' => 1,
            'statement' => 'What is the capital city of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'B',
        ]);

        // Check for duplicate with very similar statement
        $questionData = [
            'statement' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'B',
        ];

        $result = $this->service->checkForDuplicate($exam, $questionData);

        $this->assertTrue($result['is_duplicate']);
        $this->assertGreaterThan(0.85, $result['similarity_score']);
    }

    /** @test */
    public function it_does_not_detect_duplicate_for_different_questions()
    {
        // Create a career and exam
        $career = Career::create([
            'name' => 'Test Career',
            'slug' => 'test-career',
            'description' => 'Test career description',
        ]);

        $exam = Exam::create([
            'career_id' => $career->id,
            'title' => 'Test Exam',
            'description' => 'Test exam description',
            'time_limit_minutes' => 60,
            'active' => true,
        ]);

        // Create an existing question
        Question::create([
            'exam_id' => $exam->id,
            'question_number' => 1,
            'statement' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'B',
        ]);

        // Check for duplicate with completely different question
        $questionData = [
            'statement' => 'What is the largest planet in our solar system?',
            'option_a' => 'Earth',
            'option_b' => 'Mars',
            'option_c' => 'Jupiter',
            'option_d' => 'Saturn',
            'option_e' => 'Venus',
            'correct_answer' => 'C',
        ];

        $result = $this->service->checkForDuplicate($exam, $questionData);

        $this->assertFalse($result['is_duplicate']);
        $this->assertNull($result['type']);
        $this->assertNull($result['duplicate_question_id']);
    }

    /** @test */
    public function it_does_not_detect_duplicate_when_correct_answer_differs()
    {
        // Create a career and exam
        $career = Career::create([
            'name' => 'Test Career',
            'slug' => 'test-career',
            'description' => 'Test career description',
        ]);

        $exam = Exam::create([
            'career_id' => $career->id,
            'title' => 'Test Exam',
            'description' => 'Test exam description',
            'time_limit_minutes' => 60,
            'active' => true,
        ]);

        // Create an existing question
        Question::create([
            'exam_id' => $exam->id,
            'question_number' => 1,
            'statement' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'B',
        ]);

        // Check for duplicate with same statement but different correct answer
        $questionData = [
            'statement' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Paris',
            'option_c' => 'Berlin',
            'option_d' => 'Madrid',
            'option_e' => 'Rome',
            'correct_answer' => 'A', // Different answer
        ];

        $result = $this->service->checkForDuplicate($exam, $questionData);

        $this->assertFalse($result['is_duplicate']);
    }
}
