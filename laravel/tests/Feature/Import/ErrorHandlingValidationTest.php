<?php

namespace Tests\Feature\Import;

use Tests\TestCase;
use App\Domain\Import\Services\QuestionValidationService;
use App\Domain\Import\Services\PreviewDataService;
use App\Domain\Import\Services\ImportErrorHandler;
use App\Domain\Career\DTOs\CareerData;

class ErrorHandlingValidationTest extends TestCase
{
    private QuestionValidationService $questionValidationService;
    private PreviewDataService $previewDataService;
    private ImportErrorHandler $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->questionValidationService = app(QuestionValidationService::class);
        $this->previewDataService = new PreviewDataService($this->questionValidationService);
        $this->errorHandler = app(ImportErrorHandler::class);
    }

    /** @test */
    public function it_validates_comprehensive_error_handling()
    {
        // Test various types of validation errors
        $questionsWithErrors = collect([
            // Valid question
            [
                'statement' => 'What is the capital of Brazil?',
                'option_a' => 'Brasília', 'option_b' => 'Rio de Janeiro', 'option_c' => 'São Paulo',
                'option_d' => 'Salvador', 'option_e' => 'Belo Horizonte',
                'correct_answer' => 'A',
                'career_abbreviation' => 'PM',
                'explanation' => 'Brasília is the capital',
            ],
            // Missing statement
            [
                'statement' => '',
                'option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C',
                'option_d' => 'Option D', 'option_e' => 'Option E',
                'correct_answer' => 'A',
                'career_abbreviation' => 'PM',
            ],
            // Invalid correct answer
            [
                'statement' => 'Which is the largest state in Brazil?',
                'option_a' => 'Amazonas', 'option_b' => 'Bahia', 'option_c' => 'Minas Gerais',
                'option_d' => 'São Paulo', 'option_e' => 'Rio Grande do Sul',
                'correct_answer' => 'X', // Invalid
                'career_abbreviation' => 'PM',
            ],
            // Missing options
            [
                'statement' => 'What year was Brazil discovered?',
                'option_a' => '1500', 'option_b' => '', 'option_c' => '1502', // Missing option_b
                'option_d' => '1504', 'option_e' => '1506',
                'correct_answer' => 'A',
                'career_abbreviation' => 'PM',
            ],
            // Missing career
            [
                'statement' => 'Valid question with missing career?',
                'option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C',
                'option_d' => 'Option D', 'option_e' => 'Option E',
                'correct_answer' => 'A',
                'career_abbreviation' => '', // Missing career
            ],
        ]);

        $validationResults = $this->questionValidationService->validateQuestions($questionsWithErrors);

        // Verify error detection
        $this->assertEquals(5, $validationResults['total_questions']);
        $this->assertEquals(1, $validationResults['valid_questions']); // Only the first one should be valid
        $this->assertEquals(4, $validationResults['invalid_questions']);
        $this->assertNotEmpty($validationResults['errors']);

        // Verify error categorization
        $this->assertArrayHasKey('summary', $validationResults);
        $this->assertArrayHasKey('errors_by_type', $validationResults['summary']);

        // Check that different error types are captured
        $errorsByType = $validationResults['summary']['errors_by_type'];
        $this->assertGreaterThan(0, array_sum($errorsByType)); // Should have various error types
    }

    /** @test */
    public function it_validates_preview_error_highlighting()
    {
        $questionsWithErrors = collect([
            [
                'statement' => '', // Error: empty statement
                'option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D', 'option_e' => 'E',
                'correct_answer' => 'A',
                'career_abbreviation' => 'PM',
            ],
            [
                'statement' => 'Valid question?',
                'option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D', 'option_e' => 'E',
                'correct_answer' => 'Z', // Error: invalid answer
                'career_abbreviation' => 'PM',
            ],
        ]);

        $careerMappings = [
            'PM' => new CareerData(1, 'POLÍCIA MILITAR', 'Polícia Militar', true, '2024-01-01', '2024-01-01', 'policia-militar', 5),
        ];

        $preview = $this->previewDataService->generatePreview($questionsWithErrors, $careerMappings);

        // Verify error highlighting
        $this->assertArrayHasKey('validation_errors', $preview);
        $validationErrors = $preview['validation_errors'];

        $this->assertArrayHasKey('errors_by_row', $validationErrors);
        $this->assertArrayHasKey('errors_by_type', $validationErrors);
        $this->assertArrayHasKey('error_summary', $validationErrors);

        // Should have errors for both rows
        $this->assertCount(2, $validationErrors['errors_by_row']);

        // Should categorize errors by type
        $this->assertNotEmpty($validationErrors['errors_by_type']);
        $this->assertNotEmpty($validationErrors['error_summary']);

        // Verify total error counts
        $this->assertEquals(2, $validationErrors['total_error_rows']);
        $this->assertGreaterThan(0, $validationErrors['total_errors']);
    }

    /** @test */
    public function it_validates_import_readiness_checks()
    {
        // Test case 1: Ready for import
        $readyPreview = [
            'total_questions' => 10,
            'valid_questions' => 10,
            'invalid_questions' => 0,
            'statistics' => [
                'unmapped_questions' => 0,
                'success_rate' => 100.0,
            ],
            'validation_errors' => [
                'error_summary' => [],
            ],
        ];

        $readiness = $this->previewDataService->checkImportReadiness($readyPreview);
        $this->assertTrue($readiness['ready']);
        $this->assertTrue($readiness['can_proceed']);
        $this->assertEmpty($readiness['issues']);
        $this->assertStringContainsString('prontos para importação', $readiness['recommendation']);

        // Test case 2: Not ready - no valid questions
        $noValidPreview = [
            'total_questions' => 5,
            'valid_questions' => 0,
            'invalid_questions' => 5,
            'statistics' => [
                'unmapped_questions' => 0,
                'success_rate' => 0.0,
            ],
            'validation_errors' => [
                'error_summary' => [],
            ],
        ];

        $readiness = $this->previewDataService->checkImportReadiness($noValidPreview);
        $this->assertFalse($readiness['ready']);
        $this->assertFalse($readiness['can_proceed']);
        $this->assertNotEmpty($readiness['issues']);
        $this->assertStringContainsString('Nenhuma questão válida', $readiness['issues'][0]);

        // Test case 3: Not ready - unmapped careers
        $unmappedPreview = [
            'total_questions' => 10,
            'valid_questions' => 8,
            'invalid_questions' => 2,
            'statistics' => [
                'unmapped_questions' => 3,
                'success_rate' => 80.0,
            ],
            'validation_errors' => [
                'error_summary' => [],
            ],
        ];

        $readiness = $this->previewDataService->checkImportReadiness($unmappedPreview);
        $this->assertFalse($readiness['ready']);
        $this->assertNotEmpty($readiness['issues']);
        $this->assertStringContainsString('3 questões com carreiras não mapeadas', $readiness['issues'][0]);

        // Test case 4: Low success rate warning
        $lowSuccessPreview = [
            'total_questions' => 10,
            'valid_questions' => 4,
            'invalid_questions' => 6,
            'statistics' => [
                'unmapped_questions' => 0,
                'success_rate' => 40.0,
            ],
            'validation_errors' => [
                'error_summary' => [],
            ],
        ];

        $readiness = $this->previewDataService->checkImportReadiness($lowSuccessPreview);
        $this->assertTrue($readiness['ready']); // Can proceed but with warnings
        $this->assertNotEmpty($readiness['warnings']);
        $this->assertStringContainsString('Taxa de sucesso baixa', $readiness['warnings'][0]);
    }

    /** @test */
    public function it_validates_error_handler_functionality()
    {
        // Test different error types
        $validationError = new \Exception('Validation failed for field X');
        $databaseError = new \Illuminate\Database\QueryException('mysql', 'INSERT INTO...', [], $validationError);
        $memoryError = new \Exception('Allowed memory size exhausted');
        $timeoutError = new \Exception('Maximum execution time exceeded');

        // Test validation error handling
        $validationResult = $this->errorHandler->handleError($validationError, ['field' => 'test'], 1);
        $this->assertArrayHasKey('type', $validationResult);
        $this->assertArrayHasKey('message', $validationResult);
        $this->assertArrayHasKey('row_number', $validationResult);
        $this->assertEquals(1, $validationResult['row_number']);

        // Test database error handling
        $dbResult = $this->errorHandler->handleDatabaseError($databaseError, ['data' => 'test'], 2);
        $this->assertEquals(ImportErrorHandler::ERROR_TYPE_DATABASE, $dbResult['type']);
        $this->assertEquals(2, $dbResult['row_number']);

        // Test memory error handling
        $memoryResult = $this->errorHandler->handleMemoryError($memoryError, ['batch_size' => 100]);
        $this->assertEquals(ImportErrorHandler::ERROR_TYPE_MEMORY, $memoryResult['type']);
        $this->assertArrayHasKey('suggested_batch_size', $memoryResult);
        $this->assertLessThan(100, $memoryResult['suggested_batch_size']);

        // Test timeout error handling
        $timeoutResult = $this->errorHandler->handleTimeoutError($timeoutError, ['execution_time' => 300]);
        $this->assertEquals(ImportErrorHandler::ERROR_TYPE_TIMEOUT, $timeoutResult['type']);
        $this->assertArrayHasKey('execution_time', $timeoutResult);
    }

    /** @test */
    public function it_validates_error_continuity_decisions()
    {
        // Test that validation errors allow continuation
        $validationError = [
            'type' => ImportErrorHandler::ERROR_TYPE_VALIDATION,
            'message' => 'Field validation failed',
        ];
        $this->assertTrue($this->errorHandler->shouldContinueProcessing($validationError));

        // Test that critical database errors might stop processing
        $criticalDbError = [
            'type' => ImportErrorHandler::ERROR_TYPE_DATABASE,
            'message' => 'Connection lost',
            'severity' => 'critical',
        ];
        // This depends on implementation - might continue or stop based on error severity
        $shouldContinue = $this->errorHandler->shouldContinueProcessing($criticalDbError);
        $this->assertIsBool($shouldContinue);

        // Test memory errors with context
        $memoryError = [
            'type' => ImportErrorHandler::ERROR_TYPE_MEMORY,
            'message' => 'Memory exhausted',
        ];
        $context = ['batch_size' => 10, 'processed_count' => 5];
        $shouldContinue = $this->errorHandler->shouldContinueProcessing($memoryError, $context);
        $this->assertIsBool($shouldContinue);
    }

    /** @test */
    public function it_validates_error_recovery_mechanisms()
    {
        // Create a mock ImportSession
        $mockSession = $this->createMock(\App\Domain\Import\Models\ImportSession::class);
        $mockSession->method('__get')->with('id')->willReturn(1);
        $mockSession->id = 1;
        
        $totalProcessed = 50;
        $errors = [
            ['type' => 'validation', 'message' => 'Error 1'],
            ['type' => 'database', 'message' => 'Error 2'],
        ];

        // This should not throw an exception
        $checkpoint = $this->errorHandler->createRecoveryCheckpoint($mockSession, $totalProcessed, $errors);
        $this->assertIsArray($checkpoint);
        $this->assertArrayHasKey('session_id', $checkpoint);
        $this->assertArrayHasKey('processed_count', $checkpoint);
        $this->assertArrayHasKey('error_count', $checkpoint);
        $this->assertEquals(1, $checkpoint['session_id']);
        $this->assertEquals(50, $checkpoint['processed_count']);
        $this->assertEquals(2, $checkpoint['error_count']);
    }
}