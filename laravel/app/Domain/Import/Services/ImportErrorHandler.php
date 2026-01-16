<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Import\Models\ImportSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

final class ImportErrorHandler
{
    /**
     * Error types for categorization
     */
    public const ERROR_TYPE_VALIDATION = 'validation_error';
    public const ERROR_TYPE_DATABASE = 'database_error';
    public const ERROR_TYPE_PROCESSING = 'processing_error';
    public const ERROR_TYPE_DUPLICATE = 'duplicate_warning';
    public const ERROR_TYPE_EXAM_MAPPING = 'exam_mapping_error';
    public const ERROR_TYPE_FILE_SYSTEM = 'file_system_error';
    public const ERROR_TYPE_MEMORY = 'memory_error';
    public const ERROR_TYPE_TIMEOUT = 'timeout_error';

    /**
     * Handle and categorize import errors
     *
     * @param \Exception $exception Exception that occurred
     * @param array $context Additional context information
     * @param int|null $rowNumber Row number where error occurred
     * @return array Formatted error information
     */
    public function handleError(\Exception $exception, array $context = [], ?int $rowNumber = null): array
    {
        $errorType = $this->categorizeError($exception);
        $errorMessage = $this->formatErrorMessage($exception, $rowNumber);
        
        $errorData = [
            'type' => $errorType,
            'message' => $errorMessage,
            'row_number' => $rowNumber,
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'context' => $context,
            'timestamp' => now()->toISOString(),
        ];

        // Log error with appropriate level
        $this->logError($errorType, $errorData, $exception);

        return $errorData;
    }

    /**
     * Handle database errors with recovery mechanisms
     *
     * @param QueryException $exception Database exception
     * @param array $context Context information
     * @param int|null $rowNumber Row number
     * @return array Error information with recovery suggestions
     */
    public function handleDatabaseError(QueryException $exception, array $context = [], ?int $rowNumber = null): array
    {
        $errorData = $this->handleError($exception, $context, $rowNumber);
        
        // Add database-specific recovery information
        $errorData['recovery_suggestions'] = $this->getDatabaseRecoverySuggestions($exception);
        $errorData['should_retry'] = $this->shouldRetryDatabaseOperation($exception);
        $errorData['rollback_required'] = true;

        // Check if this is a constraint violation
        if ($this->isConstraintViolation($exception)) {
            $errorData['constraint_violation'] = true;
            $errorData['suggested_action'] = 'skip_and_continue';
        }

        return $errorData;
    }

    /**
     * Handle validation errors with detailed field information
     *
     * @param ValidationException $exception Validation exception
     * @param array $questionData Question data that failed validation
     * @param int $rowNumber Row number
     * @return array Detailed validation error information
     */
    public function handleValidationError(ValidationException $exception, array $questionData, int $rowNumber): array
    {
        $errors = [];
        
        foreach ($exception->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'type' => self::ERROR_TYPE_VALIDATION,
                    'field' => $field,
                    'message' => "Linha {$rowNumber}: {$message}",
                    'row_number' => $rowNumber,
                    'field_value' => $questionData[$field] ?? null,
                    'suggested_fix' => $this->getValidationFixSuggestion($field, $questionData[$field] ?? null),
                ];
            }
        }

        return $errors;
    }

    /**
     * Handle memory limit errors
     *
     * @param \Exception $exception Memory exception
     * @param array $context Context information
     * @return array Error information with memory optimization suggestions
     */
    public function handleMemoryError(\Exception $exception, array $context = []): array
    {
        $errorData = $this->handleError($exception, $context);
        
        $errorData['memory_usage'] = memory_get_usage(true);
        $errorData['memory_peak'] = memory_get_peak_usage(true);
        $errorData['memory_limit'] = ini_get('memory_limit');
        $errorData['suggested_batch_size'] = $this->calculateOptimalBatchSize($context);
        $errorData['recovery_action'] = 'reduce_batch_size';

        return $errorData;
    }

    /**
     * Handle timeout errors
     *
     * @param \Exception $exception Timeout exception
     * @param array $context Context information
     * @return array Error information with timeout recovery suggestions
     */
    public function handleTimeoutError(\Exception $exception, array $context = []): array
    {
        $errorData = $this->handleError($exception, $context);
        
        $errorData['execution_time'] = $context['execution_time'] ?? null;
        $errorData['max_execution_time'] = ini_get('max_execution_time');
        $errorData['suggested_batch_size'] = $this->calculateOptimalBatchSize($context, 'timeout');
        $errorData['recovery_action'] = 'resume_from_checkpoint';

        return $errorData;
    }

    /**
     * Create error recovery checkpoint
     *
     * @param ImportSession $session Import session
     * @param int $processedCount Number of questions processed so far
     * @param array $errors Accumulated errors
     * @return array Checkpoint data
     */
    public function createRecoveryCheckpoint(ImportSession $session, int $processedCount, array $errors): array
    {
        $checkpoint = [
            'session_id' => $session->id,
            'processed_count' => $processedCount,
            'error_count' => count($errors),
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
            'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true)),
        ];

        // Store checkpoint in session
        $session->update([
            'validation_errors' => array_merge($session->validation_errors ?? [], ['checkpoint' => $checkpoint])
        ]);

        Log::info('Recovery checkpoint created', $checkpoint);

        return $checkpoint;
    }

    /**
     * Recover from checkpoint and continue processing
     *
     * @param ImportSession $session Import session
     * @param array $checkpoint Checkpoint data
     * @return bool True if recovery is possible
     */
    public function recoverFromCheckpoint(ImportSession $session, array $checkpoint): bool
    {
        try {
            // Validate checkpoint data
            if (!$this->isValidCheckpoint($checkpoint)) {
                return false;
            }

            // Update session status
            $session->update([
                'status' => ImportSession::STATUS_PROCESSING,
            ]);

            Log::info('Recovering from checkpoint', [
                'session_id' => $session->id,
                'checkpoint_processed_count' => $checkpoint['processed_count'],
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to recover from checkpoint', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Determine if processing should continue after error
     *
     * @param array $errorData Error information
     * @param array $context Processing context
     * @return bool True if processing should continue
     */
    public function shouldContinueProcessing(array $errorData, array $context = []): bool
    {
        $errorType = $errorData['type'];
        
        // Always continue for validation and duplicate errors
        if (in_array($errorType, [self::ERROR_TYPE_VALIDATION, self::ERROR_TYPE_DUPLICATE])) {
            return true;
        }

        // Continue for database constraint violations
        if ($errorType === self::ERROR_TYPE_DATABASE && ($errorData['constraint_violation'] ?? false)) {
            return true;
        }

        // Continue for exam mapping errors (skip unmappable questions)
        if ($errorType === self::ERROR_TYPE_EXAM_MAPPING) {
            return true;
        }

        // Don't continue for critical system errors
        if (in_array($errorType, [self::ERROR_TYPE_MEMORY, self::ERROR_TYPE_TIMEOUT, self::ERROR_TYPE_FILE_SYSTEM])) {
            return false;
        }

        // For other database errors, check if retry is recommended
        if ($errorType === self::ERROR_TYPE_DATABASE) {
            return $errorData['should_retry'] ?? false;
        }

        // Default to continuing for unknown errors (with logging)
        Log::warning('Unknown error type, defaulting to continue processing', $errorData);
        return true;
    }

    /**
     * Get error statistics and summary
     *
     * @param array $errors Array of error data
     * @return array Error statistics
     */
    public function getErrorStatistics(array $errors): array
    {
        $stats = [
            'total_errors' => count($errors),
            'by_type' => [],
            'by_row' => [],
            'critical_errors' => 0,
            'recoverable_errors' => 0,
        ];

        foreach ($errors as $error) {
            $type = $error['type'] ?? 'unknown';
            $rowNumber = $error['row_number'] ?? 'unknown';

            // Count by type
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;

            // Count by row
            if ($rowNumber !== 'unknown') {
                $stats['by_row'][$rowNumber] = ($stats['by_row'][$rowNumber] ?? 0) + 1;
            }

            // Categorize as critical or recoverable
            if ($this->isCriticalError($type)) {
                $stats['critical_errors']++;
            } else {
                $stats['recoverable_errors']++;
            }
        }

        return $stats;
    }

    /**
     * Categorize exception by type
     *
     * @param \Exception $exception Exception to categorize
     * @return string Error type constant
     */
    private function categorizeError(\Exception $exception): string
    {
        if ($exception instanceof ValidationException) {
            return self::ERROR_TYPE_VALIDATION;
        }

        if ($exception instanceof QueryException) {
            return self::ERROR_TYPE_DATABASE;
        }

        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'memory') || str_contains($message, 'allowed memory size')) {
            return self::ERROR_TYPE_MEMORY;
        }

        if (str_contains($message, 'timeout') || str_contains($message, 'maximum execution time')) {
            return self::ERROR_TYPE_TIMEOUT;
        }

        if (str_contains($message, 'file') || str_contains($message, 'storage')) {
            return self::ERROR_TYPE_FILE_SYSTEM;
        }

        if (str_contains($message, 'duplicate')) {
            return self::ERROR_TYPE_DUPLICATE;
        }

        return self::ERROR_TYPE_PROCESSING;
    }

    /**
     * Format error message for display
     *
     * @param \Exception $exception Exception
     * @param int|null $rowNumber Row number
     * @return string Formatted error message
     */
    private function formatErrorMessage(\Exception $exception, ?int $rowNumber = null): string
    {
        $message = $exception->getMessage();
        
        if ($rowNumber) {
            return "Linha {$rowNumber}: {$message}";
        }

        return $message;
    }

    /**
     * Log error with appropriate level
     *
     * @param string $errorType Error type
     * @param array $errorData Error data
     * @param \Exception $exception Original exception
     */
    private function logError(string $errorType, array $errorData, \Exception $exception): void
    {
        $logData = [
            'error_type' => $errorType,
            'message' => $errorData['message'],
            'row_number' => $errorData['row_number'],
            'context' => $errorData['context'],
        ];

        if ($this->isCriticalError($errorType)) {
            Log::error('Critical import error', $logData, ['exception' => $exception]);
        } else {
            Log::warning('Import error (recoverable)', $logData);
        }
    }

    /**
     * Check if error type is critical
     *
     * @param string $errorType Error type
     * @return bool True if critical
     */
    private function isCriticalError(string $errorType): bool
    {
        return in_array($errorType, [
            self::ERROR_TYPE_MEMORY,
            self::ERROR_TYPE_TIMEOUT,
            self::ERROR_TYPE_FILE_SYSTEM,
        ]);
    }

    /**
     * Get database recovery suggestions
     *
     * @param QueryException $exception Database exception
     * @return array Recovery suggestions
     */
    private function getDatabaseRecoverySuggestions(QueryException $exception): array
    {
        $suggestions = [];
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'duplicate')) {
            $suggestions[] = 'Skip duplicate records and continue processing';
        }

        if (str_contains($message, 'foreign key')) {
            $suggestions[] = 'Verify career and exam mappings are correct';
        }

        if (str_contains($message, 'connection')) {
            $suggestions[] = 'Retry with database connection recovery';
        }

        if (str_contains($message, 'deadlock')) {
            $suggestions[] = 'Retry with smaller batch size to reduce lock contention';
        }

        return $suggestions;
    }

    /**
     * Check if database operation should be retried
     *
     * @param QueryException $exception Database exception
     * @return bool True if should retry
     */
    private function shouldRetryDatabaseOperation(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        // Retry for connection issues
        if (str_contains($message, 'connection') || str_contains($message, 'server has gone away')) {
            return true;
        }

        // Retry for deadlocks
        if (str_contains($message, 'deadlock')) {
            return true;
        }

        // Don't retry for constraint violations
        if (str_contains($message, 'duplicate') || str_contains($message, 'foreign key')) {
            return false;
        }

        return false;
    }

    /**
     * Check if exception is a constraint violation
     *
     * @param QueryException $exception Database exception
     * @return bool True if constraint violation
     */
    private function isConstraintViolation(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());
        
        return str_contains($message, 'duplicate') || 
               str_contains($message, 'foreign key') ||
               str_contains($message, 'unique constraint');
    }

    /**
     * Get validation fix suggestion
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @return string Fix suggestion
     */
    private function getValidationFixSuggestion(string $field, $value): string
    {
        if (empty($value)) {
            return "Campo '{$field}' é obrigatório e não pode estar vazio";
        }

        if ($field === 'correct_answer') {
            return "Resposta correta deve ser A, B, C, D ou E";
        }

        if (str_contains($field, 'option_')) {
            return "Alternativa deve conter texto válido";
        }

        if ($field === 'statement') {
            return "Enunciado deve ter pelo menos 10 caracteres";
        }

        return "Verifique o formato e conteúdo do campo '{$field}'";
    }

    /**
     * Calculate optimal batch size based on context
     *
     * @param array $context Processing context
     * @param string $reason Reason for calculation (memory, timeout, etc.)
     * @return int Suggested batch size
     */
    private function calculateOptimalBatchSize(array $context, string $reason = 'memory'): int
    {
        $currentBatchSize = $context['batch_size'] ?? 50;
        
        if ($reason === 'memory') {
            // Reduce batch size by half for memory issues
            return max(5, intval($currentBatchSize / 2));
        }

        if ($reason === 'timeout') {
            // Reduce batch size by 75% for timeout issues
            return max(5, intval($currentBatchSize * 0.25));
        }

        // Default reduction
        return max(10, intval($currentBatchSize * 0.5));
    }

    /**
     * Validate checkpoint data
     *
     * @param array $checkpoint Checkpoint data
     * @return bool True if valid
     */
    private function isValidCheckpoint(array $checkpoint): bool
    {
        $requiredFields = ['session_id', 'processed_count', 'timestamp'];
        
        foreach ($requiredFields as $field) {
            if (!isset($checkpoint[$field])) {
                return false;
            }
        }

        return true;
    }
}