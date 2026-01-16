<?php

namespace Tests\Unit\Import;

use Tests\TestCase;
use App\Domain\Import\Services\ImportReportService;
use App\Domain\Import\Models\ImportSession;
use App\Domain\Import\Models\ImportResult;
use App\Domain\Auth\Models\User;

class ImportReportServiceTest extends TestCase
{

    private ImportReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ImportReportService();
    }

    public function test_generates_detailed_error_report()
    {
        // Create mock import result with errors
        $result = new ImportResult([
            'total_processed' => 5,
            'successful_imports' => 2,
            'failed_imports' => 3,
            'errors' => [
                [
                    'row' => 1,
                    'type' => 'Validation Error',
                    'field' => 'statement',
                    'message' => 'Statement is required',
                    'data' => null,
                    'timestamp' => now()->toISOString(),
                ],
                [
                    'row' => 3,
                    'type' => 'Validation Error',
                    'field' => 'correct_answer',
                    'message' => 'Invalid answer option',
                    'data' => ['correct_answer' => 'F'],
                    'timestamp' => now()->toISOString(),
                ],
                [
                    'row' => 5,
                    'type' => 'Database Error',
                    'field' => null,
                    'message' => 'Failed to save question',
                    'data' => ['error' => 'Connection timeout'],
                    'timestamp' => now()->toISOString(),
                ],
            ],
            'success_details' => [],
            'processing_time' => 30,
        ]);

        // Generate error report
        $errorReport = $this->service->generateErrorReport($result);

        // Assert error report structure
        $this->assertCount(3, $errorReport);
        
        // Check first error
        $this->assertEquals(1, $errorReport[0]['row_number']);
        $this->assertEquals('Validation Error', $errorReport[0]['error_type']);
        $this->assertEquals('statement', $errorReport[0]['field']);
        $this->assertEquals('Statement is required', $errorReport[0]['message']);

        // Check that errors are sorted by row number
        $this->assertEquals(1, $errorReport[0]['row_number']);
        $this->assertEquals(3, $errorReport[1]['row_number']);
        $this->assertEquals(5, $errorReport[2]['row_number']);
    }

    public function test_groups_success_details_by_exam()
    {
        // Create mock import result with success details for multiple exams
        $result = new ImportResult([
            'total_processed' => 6,
            'successful_imports' => 6,
            'failed_imports' => 0,
            'errors' => [],
            'success_details' => [
                [
                    'exam_id' => 1,
                    'exam_name' => 'PM Test Exam',
                    'career_name' => 'Polícia Militar',
                    'question_number' => 1,
                    'statement' => 'PM Question 1',
                    'row' => 1,
                ],
                [
                    'exam_id' => 1,
                    'exam_name' => 'PM Test Exam',
                    'career_name' => 'Polícia Militar',
                    'question_number' => 2,
                    'statement' => 'PM Question 2',
                    'row' => 2,
                ],
                [
                    'exam_id' => 2,
                    'exam_name' => 'CBM Test Exam',
                    'career_name' => 'Corpo de Bombeiros',
                    'question_number' => 1,
                    'statement' => 'CBM Question 1',
                    'row' => 3,
                ],
            ],
            'processing_time' => 25,
        ]);

        // Group success details by exam
        $grouped = $this->service->groupSuccessDetailsByExam($result);

        // Assert grouping
        $this->assertCount(2, $grouped);
        $this->assertArrayHasKey(1, $grouped);
        $this->assertArrayHasKey(2, $grouped);

        // Check PM exam group
        $this->assertEquals('PM Test Exam', $grouped[1]['exam_name']);
        $this->assertEquals('Polícia Militar', $grouped[1]['career_name']);
        $this->assertEquals(2, $grouped[1]['questions_imported']);
        $this->assertCount(2, $grouped[1]['question_details']);

        // Check CBM exam group
        $this->assertEquals('CBM Test Exam', $grouped[2]['exam_name']);
        $this->assertEquals('Corpo de Bombeiros', $grouped[2]['career_name']);
        $this->assertEquals(1, $grouped[2]['questions_imported']);
        $this->assertCount(1, $grouped[2]['question_details']);
    }
}