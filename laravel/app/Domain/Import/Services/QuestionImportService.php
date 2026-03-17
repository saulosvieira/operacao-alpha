<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Import\Models\ImportSession;
use App\Domain\Import\Models\ImportResult;
use App\Domain\Import\Imports\ExcelQuestionImport;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Domain\Import\Services\ImportErrorHandler;
use App\Domain\Import\Services\DuplicateDetectionService;
use App\Domain\Import\Services\ExamCountMaintenanceService;
use App\Domain\Import\Services\ImportProgressTracker;
use App\Domain\Import\Services\DatabaseQueryOptimizer;
use Carbon\Carbon;

final class QuestionImportService
{
    private const BATCH_SIZE = 50;
    private const SESSION_EXPIRY_HOURS = 24;

    public function __construct(
        private readonly FileValidationService $fileValidationService,
        private readonly CareerMappingService $careerMappingService,
        private readonly QuestionValidationService $questionValidationService,
        private readonly PreviewDataService $previewDataService,
        private readonly ImportErrorHandler $errorHandler,
        private readonly DuplicateDetectionService $duplicateDetectionService,
        private readonly ExamCountMaintenanceService $examCountMaintenanceService,
        private readonly ImportProgressTracker $progressTracker,
        private readonly DatabaseQueryOptimizer $queryOptimizer
    ) {
    }

    /**
     * Process uploaded file and create import session
     *
     * @param UploadedFile $file Uploaded Excel file
     * @param int $userId User ID who initiated the import
     * @return ImportSession Created import session
     * @throws \Exception If file processing fails
     */
    public function processFile(UploadedFile $file, int $userId): ImportSession
    {
        // Validate file
        $validation = $this->fileValidationService->validateFile($file);
        if (!$validation['valid']) {
            throw new \Exception('File validation failed: ' . implode(', ', $validation['errors']));
        }

        // Store file temporarily
        $filePath = $file->store('temp/imports', 'local');
        
        try {
            // Parse Excel file to get row count
            $import = new ExcelQuestionImport();
            Excel::import($import, $file);
            $data = $import->getProcessedData();
            
            // Create import session
            $session = ImportSession::create([
                'filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'total_rows' => $data->count(),
                'status' => ImportSession::STATUS_UPLOADED,
                'created_by' => $userId,
                'expires_at' => Carbon::now()->addHours(self::SESSION_EXPIRY_HOURS),
            ]);

            Log::info('Import session created', [
                'session_id' => $session->id,
                'filename' => $session->filename,
                'total_rows' => $session->total_rows,
                'user_id' => $userId,
            ]);

            return $session;

        } catch (\Exception $e) {
            // Clean up file if session creation fails
            Storage::disk('local')->delete($filePath);
            throw $e;
        }
    }

    /**
     * Extract career abbreviations from import session
     *
     * @param ImportSession $session Import session
     * @return array Array of unique career abbreviations
     * @throws \Exception If session is invalid or expired
     */
    public function extractCareerAbbreviations(ImportSession $session): array
    {
        $this->validateSession($session);

        $data = $this->loadSessionData($session);
        return $this->careerMappingService->extractAbbreviations($data);
    }

    /**
     * Validate career mappings for import session
     *
     * @param ImportSession $session Import session
     * @param array $mappings Career mappings (abbreviation => career_id)
     * @return array Validation result
     * @throws \Exception If session is invalid or expired
     */
    public function validateMappings(ImportSession $session, array $mappings): array
    {
        $this->validateSession($session);

        $validation = $this->careerMappingService->validateMappings($mappings);
        
        if ($validation['valid']) {
            // Update session with mappings
            $session->update([
                'career_mappings' => $mappings,
                'status' => ImportSession::STATUS_MAPPED,
            ]);

            Log::info('Career mappings validated and saved', [
                'session_id' => $session->id,
                'mappings_count' => count($mappings),
            ]);
        }

        return $validation;
    }

    /**
     * Generate preview data for import session
     *
     * @param ImportSession $session Import session with validated mappings
     * @return array Preview data with statistics and sample questions
     * @throws \Exception If session is invalid or mappings not set
     */
    public function generatePreview(ImportSession $session): array
    {
        $this->validateSession($session);
        
        if (!$session->career_mappings) {
            throw new \Exception('Career mappings must be set before generating preview');
        }

        $data = $this->loadSessionData($session);
        $mappedData = $this->careerMappingService->applyMappings($data, $session->career_mappings);
        
        $preview = $this->previewDataService->generatePreview($mappedData, $session->career_mappings);
        
        // Update session status
        $session->update([
            'status' => ImportSession::STATUS_PREVIEWED,
            'validation_errors' => $preview['validation_errors'] ?? [],
        ]);

        Log::info('Preview generated', [
            'session_id' => $session->id,
            'total_questions' => $preview['total_questions'],
            'valid_questions' => $preview['valid_questions'],
            'invalid_questions' => $preview['invalid_questions'],
        ]);

        return $preview;
    }

    /**
     * Execute the import process for a session
     *
     * @param ImportSession $session Import session ready for processing
     * @param array $examMappings Optional exam mappings (career_id => exam_id)
     * @return ImportResult Import result with statistics and details
     * @throws \Exception If import fails
     */
    public function executeImport(ImportSession $session, array $examMappings = []): ImportResult
    {
        $this->validateSession($session);
        
        if (!$session->career_mappings) {
            throw new \Exception('Career mappings must be set before executing import');
        }

        // Update session status to processing
        $session->update(['status' => ImportSession::STATUS_PROCESSING]);

        $startTime = microtime(true);
        $totalProcessed = 0;
        $successfulImports = 0;
        $failedImports = 0;
        $errors = [];
        $successDetails = [];
        $currentBatchSize = self::BATCH_SIZE;

        try {
            $data = $this->loadSessionData($session);
            
            // Initialize progress tracking
            $this->progressTracker->initializeProgress($session, $data->count());
            
            // Optimize database for bulk operations
            $this->queryOptimizer->optimizeForBulkOperations();
            
            $mappedData = $this->careerMappingService->applyMappings($data, $session->career_mappings);
            
            // Process in batches with adaptive batch sizing
            $batches = $mappedData->chunk($currentBatchSize);
            $totalBatches = $batches->count();
            
            foreach ($batches as $batchIndex => $batch) {
                Log::info('Processing batch', [
                    'session_id' => $session->id,
                    'batch_index' => $batchIndex,
                    'batch_size' => $batch->count(),
                    'current_batch_size' => $currentBatchSize,
                ]);

                try {
                    $batchResult = $this->processBatch($batch, $examMappings, $totalProcessed + 1);
                    
                    $totalProcessed += $batchResult['processed'];
                    $successfulImports += $batchResult['successful'];
                    $failedImports += $batchResult['failed'];
                    $errors = array_merge($errors, $batchResult['errors']);
                    $successDetails = array_merge($successDetails, $batchResult['success_details']);

                    // Update progress tracking
                    $this->progressTracker->updateBatchProgress(
                        $session->id,
                        $batchIndex,
                        $totalBatches,
                        $batchResult['processed'],
                        $batchResult['successful'],
                        $batchResult['failed']
                    );

                    // Create recovery checkpoint every 5 batches
                    if ($batchIndex % 5 === 0) {
                        $this->errorHandler->createRecoveryCheckpoint($session, $totalProcessed, $errors);
                    }

                    // Check memory usage and adjust batch size if needed
                    if ($this->progressTracker->isMemoryUsageHigh()) {
                        $newBatchSize = $this->progressTracker->getSuggestedBatchSize($currentBatchSize);
                        
                        if ($newBatchSize !== $currentBatchSize) {
                            Log::info('Adjusting batch size due to memory usage', [
                                'old_batch_size' => $currentBatchSize,
                                'new_batch_size' => $newBatchSize,
                                'memory_usage' => memory_get_usage(true),
                            ]);
                            
                            $currentBatchSize = $newBatchSize;
                            
                            // Perform memory cleanup
                            $this->progressTracker->performMemoryCleanup();
                        }
                    }

                } catch (\Exception $e) {
                    // Handle batch-level errors
                    if ($this->isMemoryError($e)) {
                        $errorData = $this->errorHandler->handleMemoryError($e, [
                            'batch_size' => $currentBatchSize,
                            'batch_index' => $batchIndex,
                            'total_processed' => $totalProcessed,
                        ]);
                        
                        // Reduce batch size and continue
                        $currentBatchSize = $errorData['suggested_batch_size'];
                        $errors[] = $errorData;
                        
                        Log::warning('Reducing batch size due to memory constraints', [
                            'old_batch_size' => $batch->count(),
                            'new_batch_size' => $currentBatchSize,
                        ]);
                        
                        // Re-chunk remaining data with smaller batch size
                        $remainingData = $mappedData->skip($totalProcessed);
                        $batches = $remainingData->chunk($currentBatchSize);
                        continue;
                        
                    } elseif ($this->isTimeoutError($e)) {
                        $errorData = $this->errorHandler->handleTimeoutError($e, [
                            'batch_size' => $currentBatchSize,
                            'batch_index' => $batchIndex,
                            'total_processed' => $totalProcessed,
                            'execution_time' => microtime(true) - $startTime,
                        ]);
                        
                        $errors[] = $errorData;
                        
                        Log::warning('Import timeout detected, stopping processing', [
                            'total_processed' => $totalProcessed,
                            'execution_time' => microtime(true) - $startTime,
                        ]);
                        
                        break; // Stop processing on timeout
                        
                    } else {
                        // For other critical errors, stop processing
                        throw $e;
                    }
                }
            }

            $processingTime = (int) round(microtime(true) - $startTime);

            // Create import result
            $result = ImportResult::create([
                'session_id' => $session->id,
                'total_processed' => $totalProcessed,
                'successful_imports' => $successfulImports,
                'failed_imports' => $failedImports,
                'errors' => $errors,
                'success_details' => $successDetails,
                'processing_time' => $processingTime,
            ]);

            // Update session status
            $session->update([
                'status' => $successfulImports > 0 ? ImportSession::STATUS_COMPLETED : ImportSession::STATUS_FAILED,
            ]);

            // Update exam question counts
            $countUpdateResult = $this->examCountMaintenanceService->updateExamQuestionCounts($successDetails);

            // Complete progress tracking
            $this->progressTracker->completeProgress($session->id, [
                'total_processed' => $totalProcessed,
                'successful_imports' => $successfulImports,
                'failed_imports' => $failedImports,
                'processing_time' => $processingTime,
                'final_batch_size' => $currentBatchSize,
            ]);

            Log::info('Import completed', [
                'session_id' => $session->id,
                'total_processed' => $totalProcessed,
                'successful_imports' => $successfulImports,
                'failed_imports' => $failedImports,
                'processing_time' => $processingTime,
                'final_batch_size' => $currentBatchSize,
            ]);

            return $result;

        } catch (\Exception $e) {
            $session->update(['status' => ImportSession::STATUS_FAILED]);
            
            Log::error('Import failed', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'total_processed' => $totalProcessed,
            ]);

            throw $e;
        } finally {
            // Always restore normal database settings
            $this->queryOptimizer->restoreNormalSettings();
        }
    }

    /**
     * Process a batch of questions
     *
     * @param Collection $batch Batch of questions to process
     * @param array $examMappings Exam mappings
     * @param int $startingRowNumber Starting row number for this batch
     * @return array Batch processing result
     */
    private function processBatch(Collection $batch, array $examMappings, int $startingRowNumber): array
    {
        $processed = 0;
        $successful = 0;
        $failed = 0;
        $errors = [];
        $successDetails = [];

        DB::beginTransaction();

        try {
            foreach ($batch as $index => $questionData) {
                $rowNumber = $startingRowNumber + $index;
                $processed++;

                try {
                    $result = $this->processQuestion($questionData, $examMappings, $rowNumber);
                    
                    if ($result['success']) {
                        $successful++;
                        $successDetails[] = $result['details'];
                        // Collect warnings from successful imports (e.g. invalid URL set to null)
                        if (!empty($result['warnings'])) {
                            $errors = array_merge($errors, $result['warnings']);
                        }
                    } else {
                        $failed++;
                        $errors = array_merge($errors, $result['errors']);
                    }

                } catch (\Exception $e) {
                    $failed++;
                    
                    // Use error handler for consistent error processing
                    $errorData = $this->errorHandler->handleError($e, [
                        'question_data' => $questionData,
                        'batch_index' => $index,
                        'starting_row' => $startingRowNumber,
                    ], $rowNumber);
                    
                    $errors[] = $errorData;

                    // Check if we should continue processing
                    if (!$this->errorHandler->shouldContinueProcessing($errorData, [
                        'batch_size' => $batch->count(),
                        'processed_count' => $processed,
                    ])) {
                        Log::warning('Stopping batch processing due to critical error', [
                            'row_number' => $rowNumber,
                            'error_type' => $errorData['type'],
                        ]);
                        break;
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Handle batch-level errors
            $batchError = $this->errorHandler->handleError($e, [
                'batch_size' => $batch->count(),
                'starting_row' => $startingRowNumber,
                'processed_in_batch' => $processed,
            ]);
            
            $errors[] = $batchError;
            
            // If it's a critical error, re-throw to stop processing
            if (!$this->errorHandler->shouldContinueProcessing($batchError)) {
                throw $e;
            }
        }

        return [
            'processed' => $processed,
            'successful' => $successful,
            'failed' => $failed,
            'errors' => $errors,
            'success_details' => $successDetails,
        ];
    }

    /**
     * Process a single question
     *
     * @param array $questionData Question data
     * @param array $examMappings Exam mappings
     * @param int $rowNumber Row number for error reporting
     * @return array Processing result
     */
    private function processQuestion(array $questionData, array $examMappings, int $rowNumber): array
    {
        // Validate question data
        $validation = $this->questionValidationService->validateQuestion($questionData, $rowNumber);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => array_map(fn($error) => [
                    'row_number' => $rowNumber,
                    'type' => ImportErrorHandler::ERROR_TYPE_VALIDATION,
                    'message' => $error,
                    'data' => $questionData,
                ], $validation['errors']),
            ];
        }

        // Get target exam
        $exam = $this->getTargetExam($questionData, $examMappings);
        
        if (!$exam) {
            return [
                'success' => false,
                'errors' => [[
                    'row_number' => $rowNumber,
                    'type' => ImportErrorHandler::ERROR_TYPE_EXAM_MAPPING,
                    'message' => "Linha {$rowNumber}: Não foi possível determinar o simulado de destino para a carreira",
                    'data' => $questionData,
                ]],
            ];
        }

        // Check for duplicates
        $duplicateResult = $this->duplicateDetectionService->checkForDuplicate($exam, $validation['data']);
        
        if ($duplicateResult['is_duplicate']) {
            // Log the duplicate detection
            $this->duplicateDetectionService->logDuplicateDetection($duplicateResult, $questionData, $rowNumber);
            
            return [
                'success' => false,
                'errors' => [[
                    'row_number' => $rowNumber,
                    'type' => ImportErrorHandler::ERROR_TYPE_DUPLICATE,
                    'message' => "Linha {$rowNumber}: {$duplicateResult['reason']}",
                    'data' => array_merge($questionData, [
                        'duplicate_question_id' => $duplicateResult['duplicate_question_id'],
                        'duplicate_question_number' => $duplicateResult['duplicate_question_number'],
                        'similarity_score' => $duplicateResult['similarity_score'],
                    ]),
                ]],
            ];
        }

        try {
            // Get next question number
            $questionNumber = $this->getNextQuestionNumber($exam);

            // Validate support_pdf_url if provided
            $warnings = [];
            $supportPdfUrl = $validation['data']['support_pdf_url'] ?? null;

            if ($supportPdfUrl !== null && $supportPdfUrl !== '') {
                if (!filter_var($supportPdfUrl, FILTER_VALIDATE_URL)) {
                    $warnings[] = [
                        'row_number' => $rowNumber,
                        'type' => ImportErrorHandler::ERROR_TYPE_VALIDATION,
                        'message' => "Linha {$rowNumber}: URL inválida no campo link_pdf_apoio ('{$supportPdfUrl}'). O campo foi importado como nulo.",
                        'data' => $questionData,
                    ];
                    $supportPdfUrl = null;

                    Log::info('Invalid support_pdf_url set to null during import', [
                        'row_number' => $rowNumber,
                        'original_url' => $validation['data']['support_pdf_url'],
                    ]);
                }
            } else {
                $supportPdfUrl = null;
            }

            // Create question
            $question = Question::create([
                'exam_id' => $exam->id,
                'question_number' => $questionNumber,
                'statement' => $validation['data']['statement'],
                'option_a' => $validation['data']['option_a'],
                'option_b' => $validation['data']['option_b'],
                'option_c' => $validation['data']['option_c'],
                'option_d' => $validation['data']['option_d'],
                'option_e' => $validation['data']['option_e'],
                'correct_answer' => $validation['data']['correct_answer'],
                'explanation' => $validation['data']['explanation'] ?: null,
                'support_text' => $validation['data']['support_text'] ?? null,
                'support_pdf_url' => $supportPdfUrl,
            ]);

            return [
                'success' => true,
                'details' => [
                    'question_id' => $question->id,
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->title,
                    'question_number' => $questionNumber,
                    'row_number' => $rowNumber,
                ],
                'warnings' => $warnings,
            ];

        } catch (\Exception $e) {
            // Handle database errors specifically
            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorData = $this->errorHandler->handleDatabaseError($e, [
                    'question_data' => $questionData,
                    'exam_id' => $exam->id,
                ], $rowNumber);
            } else {
                $errorData = $this->errorHandler->handleError($e, [
                    'question_data' => $questionData,
                    'exam_id' => $exam->id,
                ], $rowNumber);
            }

            return [
                'success' => false,
                'errors' => [$errorData],
            ];
        }
    }

    /**
     * Get target exam for question
     *
     * @param array $questionData Question data with career_id
     * @param array $examMappings Optional exam mappings
     * @return Exam|null Target exam or null if not found
     */
    private function getTargetExam(array $questionData, array $examMappings): ?Exam
    {
        $careerId = $questionData['career_id'] ?? null;
        
        if (!$careerId) {
            return null;
        }

        // Use explicit exam mapping if provided
        if (isset($examMappings[$careerId])) {
            return Exam::find($examMappings[$careerId]);
        }

        // Otherwise, find the first active exam for the career
        return Exam::where('career_id', $careerId)
            ->where('active', true)
            ->first();
    }

    /**
     * Get next question number for exam
     *
     * @param Exam $exam Target exam
     * @return int Next question number
     */
    private function getNextQuestionNumber(Exam $exam): int
    {
        $maxNumber = Question::where('exam_id', $exam->id)
            ->max('question_number');
            
        return ($maxNumber ?? 0) + 1;
    }

    /**
     * Load session data from Excel file
     *
     * @param ImportSession $session Import session
     * @return Collection Question data collection
     * @throws \Exception If file cannot be loaded
     */
    private function loadSessionData(ImportSession $session): Collection
    {
        if (!Storage::disk('local')->exists($session->file_path)) {
            throw new \Exception('Import file not found or expired');
        }

        try {
            $import = new ExcelQuestionImport();
            Excel::import($import, Storage::disk('local')->path($session->file_path));
            return $import->getProcessedData();
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to load import data: {$e->getMessage()}");
        }
    }

    /**
     * Validate import session
     *
     * @param ImportSession $session Import session to validate
     * @throws \Exception If session is invalid
     */
    private function validateSession(ImportSession $session): void
    {
        if ($session->isExpired()) {
            throw new \Exception('Import session has expired');
        }

        if (!Storage::disk('local')->exists($session->file_path)) {
            throw new \Exception('Import file not found');
        }
    }

    /**
     * Clean up expired sessions and their files
     *
     * @return int Number of sessions cleaned up
     */
    public function cleanupExpiredSessions(): int
    {
        $expiredSessions = ImportSession::where('expires_at', '<', Carbon::now())->get();
        $cleanedCount = 0;

        foreach ($expiredSessions as $session) {
            try {
                // Delete file if exists
                if (Storage::disk('local')->exists($session->file_path)) {
                    Storage::disk('local')->delete($session->file_path);
                }

                // Delete session
                $session->delete();
                $cleanedCount++;

                Log::info('Cleaned up expired import session', [
                    'session_id' => $session->id,
                    'filename' => $session->filename,
                ]);

            } catch (\Exception $e) {
                Log::warning('Failed to cleanup expired session', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $cleanedCount;
    }

    /**
     * Get import session by ID
     *
     * @param int $sessionId Session ID
     * @return ImportSession Import session
     * @throws \Exception If session not found
     */
    public function getSession(int $sessionId): ImportSession
    {
        $session = ImportSession::find($sessionId);
        
        if (!$session) {
            throw new \Exception('Import session not found');
        }

        return $session;
    }

    /**
     * Cancel import session
     *
     * @param ImportSession $session Session to cancel
     * @return bool True if cancelled successfully
     */
    public function cancelSession(ImportSession $session): bool
    {
        try {
            // Delete file if exists
            if (Storage::disk('local')->exists($session->file_path)) {
                Storage::disk('local')->delete($session->file_path);
            }

            // Delete session and related data
            $session->result()?->delete();
            $session->delete();

            Log::info('Import session cancelled', [
                'session_id' => $session->id,
                'filename' => $session->filename,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to cancel import session', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if exception is a memory error
     *
     * @param \Exception $exception Exception to check
     * @return bool True if memory error
     */
    private function isMemoryError(\Exception $exception): bool
    {
        $message = strtolower($exception->getMessage());
        return str_contains($message, 'memory') || 
               str_contains($message, 'allowed memory size') ||
               str_contains($message, 'out of memory');
    }

    /**
     * Check if exception is a timeout error
     *
     * @param \Exception $exception Exception to check
     * @return bool True if timeout error
     */
    private function isTimeoutError(\Exception $exception): bool
    {
        $message = strtolower($exception->getMessage());
        return str_contains($message, 'timeout') || 
               str_contains($message, 'maximum execution time') ||
               str_contains($message, 'time limit');
    }
}