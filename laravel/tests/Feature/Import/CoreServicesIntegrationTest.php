<?php

namespace Tests\Feature\Import;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Domain\Import\Services\QuestionImportService;
use App\Domain\Import\Services\FileValidationService;
use App\Domain\Import\Services\CareerMappingService;
use App\Domain\Import\Services\QuestionValidationService;
use App\Domain\Import\Services\PreviewDataService;
use App\Domain\Import\Models\ImportSession;
use App\Domain\Import\Models\ImportResult;
use App\Domain\Auth\Models\User;
use App\Domain\Career\Models\Career;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;

class CoreServicesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private QuestionImportService $importService;
    private FileValidationService $fileValidationService;
    private CareerMappingService $careerMappingService;
    private QuestionValidationService $questionValidationService;
    private PreviewDataService $previewDataService;
    private User $user;
    private Career $career;
    private Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        // Create services
        $this->fileValidationService = app(FileValidationService::class);
        $this->careerMappingService = app(CareerMappingService::class);
        $this->questionValidationService = app(QuestionValidationService::class);
        $this->previewDataService = app(PreviewDataService::class);
        $this->importService = app(QuestionImportService::class);

        // Create test data
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->career = Career::factory()->create(['name' => 'POLÍCIA MILITAR', 'active' => true]);
        $this->exam = Exam::factory()->create(['career_id' => $this->career->id, 'active' => true]);

        // Setup storage
        Storage::fake('local');
    }

    /** @test */
    public function it_validates_complete_import_workflow_with_valid_excel_file()
    {
        // Create a valid Excel file content
        $excelContent = $this->createValidExcelFile();
        $file = UploadedFile::fake()->createWithContent('questions.xlsx', $excelContent);

        // Step 1: File validation
        $fileValidation = $this->fileValidationService->validateFile($file);
        $this->assertTrue($fileValidation['valid'], 'File validation should pass');
        $this->assertEmpty($fileValidation['errors'], 'File validation should have no errors');

        // Step 2: Process file and create session
        $session = $this->importService->processFile($file, $this->user->id);
        $this->assertInstanceOf(ImportSession::class, $session);
        $this->assertEquals('questions.xlsx', $session->filename);
        $this->assertEquals(ImportSession::STATUS_UPLOADED, $session->status);
        $this->assertGreaterThan(0, $session->total_rows);

        // Step 3: Extract career abbreviations
        $abbreviations = $this->importService->extractCareerAbbreviations($session);
        $this->assertIsArray($abbreviations);
        $this->assertContains('PM', $abbreviations);

        // Step 4: Validate career mappings
        $mappings = ['PM' => $this->career->id];
        $mappingValidation = $this->importService->validateMappings($session, $mappings);
        $this->assertTrue($mappingValidation['valid'], 'Career mapping validation should pass');

        // Verify session was updated
        $session->refresh();
        $this->assertEquals(ImportSession::STATUS_MAPPED, $session->status);
        $this->assertNotNull($session->career_mappings);

        // Step 5: Generate preview
        $preview = $this->importService->generatePreview($session);
        $this->assertIsArray($preview);
        $this->assertArrayHasKey('statistics', $preview);
        $this->assertArrayHasKey('sample_questions', $preview);
        $this->assertGreaterThan(0, $preview['total_questions']);

        // Verify session was updated
        $session->refresh();
        $this->assertEquals(ImportSession::STATUS_PREVIEWED, $session->status);

        // Step 6: Execute import
        $result = $this->importService->executeImport($session);
        $this->assertInstanceOf(ImportResult::class, $result);
        $this->assertGreaterThan(0, $result->successful_imports);
        $this->assertEquals(0, $result->failed_imports);

        // Verify session was completed
        $session->refresh();
        $this->assertEquals(ImportSession::STATUS_COMPLETED, $session->status);

        // Verify questions were created
        $questions = Question::where('exam_id', $this->exam->id)->get();
        $this->assertGreaterThan(0, $questions->count());

        // Verify question data integrity
        $firstQuestion = $questions->first();
        $this->assertNotEmpty($firstQuestion->statement);
        $this->assertNotEmpty($firstQuestion->option_a);
        $this->assertNotEmpty($firstQuestion->option_b);
        $this->assertNotEmpty($firstQuestion->option_c);
        $this->assertNotEmpty($firstQuestion->option_d);
        $this->assertNotEmpty($firstQuestion->option_e);
        $this->assertContains($firstQuestion->correct_answer, ['A', 'B', 'C', 'D', 'E']);
    }

    /** @test */
    public function it_handles_invalid_excel_file_gracefully()
    {
        // Create an invalid file (not Excel)
        $file = UploadedFile::fake()->create('invalid.txt', 100);

        // File validation should fail
        $fileValidation = $this->fileValidationService->validateFile($file);
        $this->assertFalse($fileValidation['valid']);
        $this->assertNotEmpty($fileValidation['errors']);

        // Import service should throw exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File validation failed');
        $this->importService->processFile($file, $this->user->id);
    }

    /** @test */
    public function it_handles_excel_file_with_validation_errors()
    {
        // Create Excel file with invalid data
        $excelContent = $this->createInvalidExcelFile();
        $file = UploadedFile::fake()->createWithContent('invalid_questions.xlsx', $excelContent);

        // Process file (should succeed)
        $session = $this->importService->processFile($file, $this->user->id);
        $this->assertInstanceOf(ImportSession::class, $session);

        // Extract abbreviations and map careers
        $abbreviations = $this->importService->extractCareerAbbreviations($session);
        $mappings = ['PM' => $this->career->id];
        $this->importService->validateMappings($session, $mappings);

        // Generate preview (should show validation errors)
        $preview = $this->importService->generatePreview($session);
        $this->assertGreaterThan(0, $preview['invalid_questions']);
        $this->assertNotEmpty($preview['validation_errors']);

        // Execute import (should handle errors gracefully)
        $result = $this->importService->executeImport($session);
        $this->assertInstanceOf(ImportResult::class, $result);
        $this->assertGreaterThan(0, $result->failed_imports);
        $this->assertNotEmpty($result->errors);
    }

    /** @test */
    public function it_handles_unmapped_careers()
    {
        // Create Excel file with unmapped career
        $excelContent = $this->createExcelFileWithUnmappedCareer();
        $file = UploadedFile::fake()->createWithContent('unmapped_career.xlsx', $excelContent);

        $session = $this->importService->processFile($file, $this->user->id);
        $abbreviations = $this->importService->extractCareerAbbreviations($session);
        
        // Don't map all careers
        $mappings = []; // Empty mappings
        $mappingValidation = $this->importService->validateMappings($session, $mappings);
        
        // Should fail validation due to unmapped careers
        $this->assertFalse($mappingValidation['valid']);
        $this->assertNotEmpty($mappingValidation['errors']);
    }

    /** @test */
    public function it_validates_session_expiry()
    {
        $excelContent = $this->createValidExcelFile();
        $file = UploadedFile::fake()->createWithContent('questions.xlsx', $excelContent);

        $session = $this->importService->processFile($file, $this->user->id);
        
        // Manually expire the session
        $session->update(['expires_at' => now()->subHour()]);

        // Should throw exception for expired session
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Import session has expired');
        $this->importService->extractCareerAbbreviations($session);
    }

    /** @test */
    public function it_validates_duplicate_question_detection()
    {
        // Create a question first
        Question::factory()->create([
            'exam_id' => $this->exam->id,
            'statement' => 'What is the capital of Brazil?',
            'correct_answer' => 'A',
        ]);

        // Create Excel file with duplicate question
        $excelContent = $this->createExcelFileWithDuplicate();
        $file = UploadedFile::fake()->createWithContent('duplicate_questions.xlsx', $excelContent);

        $session = $this->importService->processFile($file, $this->user->id);
        $mappings = ['PM' => $this->career->id];
        $this->importService->validateMappings($session, $mappings);

        $result = $this->importService->executeImport($session);
        
        // Should detect and skip duplicates
        $this->assertGreaterThan(0, $result->failed_imports);
        $duplicateErrors = collect($result->errors)->filter(function ($error) {
            return str_contains($error['message'] ?? '', 'duplicada');
        });
        $this->assertGreaterThan(0, $duplicateErrors->count());
    }

    /** @test */
    public function it_validates_question_numbering()
    {
        // Create existing questions
        Question::factory()->count(5)->create(['exam_id' => $this->exam->id]);

        $excelContent = $this->createValidExcelFile();
        $file = UploadedFile::fake()->createWithContent('questions.xlsx', $excelContent);

        $session = $this->importService->processFile($file, $this->user->id);
        $mappings = ['PM' => $this->career->id];
        $this->importService->validateMappings($session, $mappings);

        $result = $this->importService->executeImport($session);
        
        // New questions should start from number 6
        $newQuestions = Question::where('exam_id', $this->exam->id)
            ->where('question_number', '>', 5)
            ->get();
        
        $this->assertGreaterThan(0, $newQuestions->count());
        $this->assertEquals(6, $newQuestions->min('question_number'));
    }

    /** @test */
    public function it_validates_error_handling_and_recovery()
    {
        $excelContent = $this->createMixedValidityExcelFile();
        $file = UploadedFile::fake()->createWithContent('mixed_questions.xlsx', $excelContent);

        $session = $this->importService->processFile($file, $this->user->id);
        $mappings = ['PM' => $this->career->id];
        $this->importService->validateMappings($session, $mappings);

        $result = $this->importService->executeImport($session);
        
        // Should have both successes and failures
        $this->assertGreaterThan(0, $result->successful_imports);
        $this->assertGreaterThan(0, $result->failed_imports);
        $this->assertEquals(
            $result->successful_imports + $result->failed_imports,
            $result->total_processed
        );

        // Should continue processing despite errors
        $this->assertNotEmpty($result->success_details);
        $this->assertNotEmpty($result->errors);
    }

    /** @test */
    public function it_validates_cleanup_functionality()
    {
        $excelContent = $this->createValidExcelFile();
        $file = UploadedFile::fake()->createWithContent('questions.xlsx', $excelContent);

        $session = $this->importService->processFile($file, $this->user->id);
        $filePath = $session->file_path;
        
        // Verify file exists
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Cancel session
        $cancelled = $this->importService->cancelSession($session);
        $this->assertTrue($cancelled);

        // Verify file was deleted
        $this->assertFalse(Storage::disk('local')->exists($filePath));

        // Verify session was deleted
        $this->assertNull(ImportSession::find($session->id));
    }

    /**
     * Create a valid Excel file content for testing
     */
    private function createValidExcelFile(): string
    {
        // This is a simplified representation - in real tests you'd create actual Excel content
        // For now, we'll create a CSV-like content that can be processed
        return "carreira,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,resposta_correta,explicacao\n" .
               "PM,What is the capital of Brazil?,Brasília,Rio de Janeiro,São Paulo,Salvador,Belo Horizonte,A,Brasília is the capital\n" .
               "PM,Which is the largest state in Brazil?,Amazonas,Bahia,Minas Gerais,São Paulo,Rio Grande do Sul,A,Amazonas is the largest\n" .
               "PM,What year was Brazil discovered?,1500,1498,1502,1504,1506,A,Brazil was discovered in 1500";
    }

    /**
     * Create an invalid Excel file content for testing
     */
    private function createInvalidExcelFile(): string
    {
        return "carreira,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,resposta_correta,explicacao\n" .
               "PM,,Option A,Option B,Option C,Option D,Option E,A,\n" . // Missing statement
               "PM,Short,A,B,C,D,E,X,\n" . // Invalid correct answer
               ",Valid question with missing career,Option A,Option B,Option C,Option D,Option E,B,"; // Missing career
    }

    /**
     * Create Excel file with unmapped career
     */
    private function createExcelFileWithUnmappedCareer(): string
    {
        return "carreira,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,resposta_correta,explicacao\n" .
               "UNKNOWN,What is the capital of Brazil?,Brasília,Rio de Janeiro,São Paulo,Salvador,Belo Horizonte,A,Brasília is the capital";
    }

    /**
     * Create Excel file with duplicate question
     */
    private function createExcelFileWithDuplicate(): string
    {
        return "carreira,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,resposta_correta,explicacao\n" .
               "PM,What is the capital of Brazil?,Brasília,Rio de Janeiro,São Paulo,Salvador,Belo Horizonte,A,Brasília is the capital";
    }

    /**
     * Create Excel file with mixed validity
     */
    private function createMixedValidityExcelFile(): string
    {
        return "carreira,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,resposta_correta,explicacao\n" .
               "PM,Valid question one?,Option A,Option B,Option C,Option D,Option E,A,Valid explanation\n" .
               "PM,,Option A,Option B,Option C,Option D,Option E,A,\n" . // Invalid - no statement
               "PM,Valid question two?,Option A,Option B,Option C,Option D,Option E,B,Valid explanation\n" .
               "PM,Invalid answer question?,Option A,Option B,Option C,Option D,Option E,X,Invalid answer\n" . // Invalid answer
               "PM,Valid question three?,Option A,Option B,Option C,Option D,Option E,C,Valid explanation";
    }
}