<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Import\Models\ImportSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class ImportProgressTracker
{
    private const CACHE_PREFIX = 'import_progress_';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Initialize progress tracking for an import session
     */
    public function initializeProgress(ImportSession $session, int $totalRows): void
    {
        $progressData = [
            'session_id' => $session->id,
            'total_rows' => $totalRows,
            'processed_rows' => 0,
            'successful_rows' => 0,
            'failed_rows' => 0,
            'current_batch' => 0,
            'total_batches' => 0,
            'start_time' => microtime(true),
            'last_update' => microtime(true),
            'estimated_completion' => null,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'status' => 'initializing',
        ];

        Cache::put($this->getCacheKey($session->id), $progressData, self::CACHE_TTL);
    }

    /**
     * Update progress for the current batch
     */
    public function updateBatchProgress(
        int $sessionId,
        int $batchIndex,
        int $totalBatches,
        int $batchProcessed,
        int $batchSuccessful,
        int $batchFailed
    ): void {
        $cacheKey = $this->getCacheKey($sessionId);
        $progressData = Cache::get($cacheKey, []);

        if (empty($progressData)) {
            Log::warning('Progress data not found for session', ['session_id' => $sessionId]);
            return;
        }

        $currentTime = microtime(true);
        $progressData['processed_rows'] += $batchProcessed;
        $progressData['successful_rows'] += $batchSuccessful;
        $progressData['failed_rows'] += $batchFailed;
        $progressData['current_batch'] = $batchIndex + 1;
        $progressData['total_batches'] = $totalBatches;
        $progressData['last_update'] = $currentTime;
        $progressData['memory_usage'] = memory_get_usage(true);
        $progressData['peak_memory'] = memory_get_peak_usage(true);
        $progressData['status'] = 'processing';

        // Calculate estimated completion time
        $elapsedTime = $currentTime - $progressData['start_time'];
        $progressPercentage = $progressData['processed_rows'] / $progressData['total_rows'];
        
        if ($progressPercentage > 0) {
            $estimatedTotalTime = $elapsedTime / $progressPercentage;
            $progressData['estimated_completion'] = $progressData['start_time'] + $estimatedTotalTime;
        }

        Cache::put($cacheKey, $progressData, self::CACHE_TTL);
    }

    /**
     * Mark import as completed
     */
    public function completeProgress(int $sessionId, array $finalStats): void
    {
        $cacheKey = $this->getCacheKey($sessionId);
        $progressData = Cache::get($cacheKey, []);

        if (empty($progressData)) {
            return;
        }

        $progressData['status'] = 'completed';
        $progressData['completion_time'] = microtime(true);
        $progressData['final_stats'] = $finalStats;
        $progressData['total_processing_time'] = $progressData['completion_time'] - $progressData['start_time'];

        Cache::put($cacheKey, $progressData, self::CACHE_TTL);
    }

    /**
     * Get current progress for a session
     */
    public function getProgress(int $sessionId): ?array
    {
        return Cache::get($this->getCacheKey($sessionId));
    }

    /**
     * Check if memory usage is approaching limits
     */
    public function isMemoryUsageHigh(): bool
    {
        $memoryLimit = $this->getMemoryLimitInBytes();
        $currentUsage = memory_get_usage(true);
        
        // Consider memory usage high if it's above 80% of the limit
        return $currentUsage > ($memoryLimit * 0.8);
    }

    /**
     * Get suggested batch size based on memory usage
     */
    public function getSuggestedBatchSize(int $currentBatchSize): int
    {
        $memoryLimit = $this->getMemoryLimitInBytes();
        $currentUsage = memory_get_usage(true);
        $usagePercentage = $currentUsage / $memoryLimit;

        if ($usagePercentage > 0.9) {
            // Very high memory usage - reduce batch size significantly
            return max(10, intval($currentBatchSize * 0.5));
        } elseif ($usagePercentage > 0.8) {
            // High memory usage - reduce batch size moderately
            return max(20, intval($currentBatchSize * 0.7));
        } elseif ($usagePercentage < 0.5 && $currentBatchSize < 100) {
            // Low memory usage - can increase batch size
            return min(100, intval($currentBatchSize * 1.2));
        }

        return $currentBatchSize;
    }

    /**
     * Force garbage collection and memory cleanup
     */
    public function performMemoryCleanup(): void
    {
        // Force garbage collection
        gc_collect_cycles();
        
        // Clear any unnecessary caches
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Get memory limit in bytes
     */
    private function getMemoryLimitInBytes(): int
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit === '-1') {
            // No memory limit
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($memoryLimit, -1));
        $value = intval(substr($memoryLimit, 0, -1));

        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
            default:
                return $value;
        }
    }

    /**
     * Get cache key for session progress
     */
    private function getCacheKey(int $sessionId): string
    {
        return self::CACHE_PREFIX . $sessionId;
    }

    /**
     * Clean up progress data for completed sessions
     */
    public function cleanupCompletedSessions(): void
    {
        // This would typically be called by a scheduled job
        // For now, we rely on cache TTL to clean up old data
    }
}