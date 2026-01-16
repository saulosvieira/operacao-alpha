<?php

namespace Tests\Feature\Import;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Domain\Import\Services\FileValidationService;
use App\Domain\Import\Imports\ExcelQuestionImport;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadPipelineTest extends TestCase
{
    private FileValidationService $fileValidationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileValidationService = app(FileValidationService::class);
        Storage::fake('local');
    }

    /** @test */
    public function it_validates_file_upload_and_processing_pipeline()
    {
        // Create a mock Excel file with proper content
        $csvContent = "carreira,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,resposta_correta,explicacao\n";
        $csvContent .= "PM,What is the capital of Brazil?,Brasília,Rio de Janeiro,São Paulo,Salvador,Belo Horizonte,A,Brasília is the capital\n";
        $csvContent .= "PM,Which is the largest state?,Amazonas,Bahia,Minas Gerais,São Paulo,Rio Grande do Sul,A,Amazonas is the largest\n";

        // Create a temporary file
        $tempFile = tmpfile();
        fwrite($tempFile, $csvContent);
        $tempPath = stream_get_meta_data($tempFile)['uri'];

        // Create UploadedFile from the temporary file
        $file = new UploadedFile(
            $tempPath,
            'questions.csv',
            'text/csv',
            null,
            true // test mode
        );

        // Step 1: File validation (will fail for CSV, but that's expected)
        $validation = $this->fileValidationService->validateFile($file);
        
        // CSV files should fail validation (we expect Excel files)
        $this->assertFalse($validation['valid']);
        $this->assertNotEmpty($validation['errors']);
        $this->assertArrayHasKey('file_info', $validation);

        // Verify file info is captured correctly
        $this->assertEquals('questions.csv', $validation['file_info']['original_name']);
        $this->assertGreaterThan(0, $validation['file_info']['size']);

        fclose($tempFile);
    }

    /** @test */
    public function it_handles_excel_import_class_correctly()
    {
        $import = new ExcelQuestionImport();
        
        // Test with valid data structure
        $mockData = collect([
            collect([
                'carreira' => 'PM',
                'enunciado' => 'What is the capital of Brazil?',
                'alternativa_a' => 'Brasília',
                'alternativa_b' => 'Rio de Janeiro',
                'alternativa_c' => 'São Paulo',
                'alternativa_d' => 'Salvador',
                'alternativa_e' => 'Belo Horizonte',
                'resposta_correta' => 'A',
                'explicacao' => 'Brasília is the capital',
            ]),
            collect([
                'carreira' => 'PC',
                'enunciado' => 'Which is the largest state?',
                'alternativa_a' => 'Amazonas',
                'alternativa_b' => 'Bahia',
                'alternativa_c' => 'Minas Gerais',
                'alternativa_d' => 'São Paulo',
                'alternativa_e' => 'Rio Grande do Sul',
                'resposta_correta' => 'A',
                'explicacao' => 'Amazonas is the largest',
            ]),
        ]);

        // Process the data
        $import->collection($mockData);

        // Verify processing results
        $processedData = $import->getProcessedData();
        $this->assertEquals(2, $processedData->count());

        $statistics = $import->getStatistics();
        $this->assertEquals(2, $statistics['total_rows']);
        $this->assertEquals(2, $statistics['unique_careers']);

        // Verify data transformation
        $firstQuestion = $processedData->first();
        $this->assertEquals('PM', $firstQuestion['career_abbreviation']);
        $this->assertEquals('What is the capital of Brazil?', $firstQuestion['statement']);
        $this->assertEquals('A', $firstQuestion['correct_answer']);

        // Verify unique career extraction
        $uniqueCareers = $import->getUniqueCareerAbbreviations();
        $this->assertCount(2, $uniqueCareers);
        $this->assertContains('PM', $uniqueCareers->toArray());
        $this->assertContains('PC', $uniqueCareers->toArray());
    }

    /** @test */
    public function it_validates_file_size_limits()
    {
        // Test file size validation
        $maxSize = $this->fileValidationService->getMaxFileSize();
        $this->assertEquals(10 * 1024 * 1024, $maxSize); // 10MB

        $formattedSize = $this->fileValidationService->getMaxFileSizeFormatted();
        $this->assertEquals('10.00 MB', $formattedSize);

        // Test allowed extensions
        $allowedExtensions = $this->fileValidationService->getAllowedExtensions();
        $this->assertEquals(['xls', 'xlsx'], $allowedExtensions);

        // Test allowed MIME types
        $allowedMimeTypes = $this->fileValidationService->getAllowedMimeTypes();
        $this->assertContains('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $allowedMimeTypes);
        $this->assertContains('application/vnd.ms-excel', $allowedMimeTypes);
    }

    /** @test */
    public function it_handles_file_validation_edge_cases()
    {
        // Test with null file (should handle gracefully)
        $emptyFile = UploadedFile::fake()->create('empty.xlsx', 0);
        $validation = $this->fileValidationService->validateFile($emptyFile);
        
        // Empty files should fail validation
        $this->assertFalse($validation['valid']);
        $this->assertNotEmpty($validation['errors']);

        // Test with very large file
        $largeFile = UploadedFile::fake()->create('large.xlsx', 20 * 1024 * 1024); // 20MB
        $validation = $this->fileValidationService->validateFile($largeFile);
        
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('muito grande', implode(' ', $validation['errors']));

        // Test with wrong extension
        $wrongExtFile = UploadedFile::fake()->create('document.pdf', 1024);
        $validation = $this->fileValidationService->validateFile($wrongExtFile);
        
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('não suportado', implode(' ', $validation['errors']));
    }

    /** @test */
    public function it_validates_excel_import_error_handling()
    {
        $import = new ExcelQuestionImport();
        
        // Test with invalid data (missing required fields)
        $invalidData = collect([
            collect([
                'carreira' => 'PM',
                // Missing 'enunciado' and other required fields
                'alternativa_a' => 'Option A',
            ]),
        ]);

        $import->collection($invalidData);
        
        $processedData = $import->getProcessedData();
        $this->assertEquals(1, $processedData->count());

        // Check that missing fields are handled
        $firstQuestion = $processedData->first();
        $this->assertEquals('PM', $firstQuestion['career_abbreviation']);
        $this->assertNull($firstQuestion['statement']); // Should be null for missing field

        // Verify statistics reflect the issues
        $statistics = $import->getStatistics();
        $this->assertEquals(1, $statistics['total_rows']);
        $this->assertEquals(0, $statistics['valid_rows']); // Should be 0 due to missing required fields
        $this->assertEquals(1, $statistics['invalid_rows']);
    }

    /** @test */
    public function it_validates_data_cleaning_and_normalization()
    {
        $import = new ExcelQuestionImport();
        
        // Test with data that needs cleaning
        $messyData = collect([
            collect([
                'carreira' => '  pm  ', // Extra whitespace
                'enunciado' => "What is the capital\n\nof Brazil?  ", // Extra whitespace and line breaks
                'alternativa_a' => '  Brasília  ',
                'alternativa_b' => 'Rio de Janeiro',
                'alternativa_c' => 'São Paulo',
                'alternativa_d' => 'Salvador',
                'alternativa_e' => 'Belo Horizonte',
                'resposta_correta' => '  a  ', // Lowercase with whitespace
                'explicacao' => "Brasília is\n\nthe capital",
            ]),
        ]);

        $import->collection($messyData);
        
        $processedData = $import->getProcessedData();
        $firstQuestion = $processedData->first();

        // Verify data was cleaned and normalized
        $this->assertEquals('pm', $firstQuestion['career_abbreviation']); // Trimmed (case preserved in ExcelQuestionImport)
        $this->assertEquals('What is the capital of Brazil?', $firstQuestion['statement']); // Cleaned whitespace
        $this->assertEquals('Brasília', $firstQuestion['option_a']); // Trimmed
        $this->assertEquals('A', $firstQuestion['correct_answer']); // Normalized to uppercase
        $this->assertEquals('Brasília is the capital', $firstQuestion['explanation']); // Cleaned line breaks
    }
}