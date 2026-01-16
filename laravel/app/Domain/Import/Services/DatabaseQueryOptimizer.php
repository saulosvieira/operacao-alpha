<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Models\Exam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class DatabaseQueryOptimizer
{
    /**
     * Bulk insert questions with optimized queries
     */
    public function bulkInsertQuestions(Collection $questionsData): array
    {
        $insertedIds = [];
        $chunkSize = 100; // Optimal chunk size for bulk inserts

        try {
            DB::beginTransaction();

            // Prepare data for bulk insert
            $questionsData->chunk($chunkSize)->each(function ($chunk) use (&$insertedIds) {
                $insertData = $chunk->map(function ($questionData) {
                    return [
                        'exam_id' => $questionData['exam_id'],
                        'question_number' => $questionData['question_number'],
                        'statement' => $questionData['statement'],
                        'option_a' => $questionData['option_a'],
                        'option_b' => $questionData['option_b'],
                        'option_c' => $questionData['option_c'],
                        'option_d' => $questionData['option_d'],
                        'option_e' => $questionData['option_e'],
                        'correct_answer' => $questionData['correct_answer'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                // Use bulk insert for better performance
                DB::table('questions')->insert($insertData);
                
                // Get the inserted IDs (this is a limitation of bulk insert)
                // We'll use the exam_id and question_number to find them
                $examIds = collect($insertData)->pluck('exam_id')->unique();
                $questionNumbers = collect($insertData)->pluck('question_number');
                
                $insertedQuestions = Question::whereIn('exam_id', $examIds)
                    ->whereIn('question_number', $questionNumbers)
                    ->orderBy('id', 'desc')
                    ->limit(count($insertData))
                    ->pluck('id')
                    ->toArray();
                
                $insertedIds = array_merge($insertedIds, $insertedQuestions);
            });

            DB::commit();

            return [
                'success' => true,
                'inserted_ids' => $insertedIds,
                'count' => count($insertedIds),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk insert failed', [
                'error' => $e->getMessage(),
                'questions_count' => $questionsData->count(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'inserted_ids' => [],
                'count' => 0,
            ];
        }
    }

    /**
     * Batch update exam question counts efficiently
     */
    public function batchUpdateExamCounts(array $examCounts): bool
    {
        try {
            DB::beginTransaction();

            // Use a single query with CASE statements for better performance
            $cases = [];
            $examIds = [];

            foreach ($examCounts as $examId => $count) {
                $cases[] = "WHEN {$examId} THEN question_count + {$count}";
                $examIds[] = $examId;
            }

            if (!empty($cases)) {
                $caseStatement = implode(' ', $cases);
                $examIdsList = implode(',', $examIds);

                DB::statement("
                    UPDATE exams 
                    SET question_count = CASE id {$caseStatement} END,
                        updated_at = NOW()
                    WHERE id IN ({$examIdsList})
                ");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Batch update exam counts failed', [
                'error' => $e->getMessage(),
                'exam_counts' => $examCounts,
            ]);

            return false;
        }
    }

    /**
     * Optimize duplicate detection queries
     */
    public function findDuplicateQuestions(int $examId, array $questionHashes): Collection
    {
        // Use a single query with IN clause instead of multiple queries
        return Question::where('exam_id', $examId)
            ->whereIn(DB::raw('MD5(CONCAT(statement, option_a, option_b, option_c, option_d, option_e))'), $questionHashes)
            ->select('id', 'statement', 'question_number')
            ->get();
    }

    /**
     * Get next question numbers for multiple exams efficiently
     */
    public function getNextQuestionNumbers(array $examIds): array
    {
        $results = DB::table('questions')
            ->select('exam_id', DB::raw('COALESCE(MAX(question_number), 0) + 1 as next_number'))
            ->whereIn('exam_id', $examIds)
            ->groupBy('exam_id')
            ->get()
            ->keyBy('exam_id')
            ->toArray();

        // Fill in missing exam IDs with 1 as the starting number
        $nextNumbers = [];
        foreach ($examIds as $examId) {
            $nextNumbers[$examId] = isset($results[$examId]) ? $results[$examId]->next_number : 1;
        }

        return $nextNumbers;
    }

    /**
     * Preload related data to avoid N+1 queries
     */
    public function preloadExamData(array $examIds): Collection
    {
        return Exam::with(['career', 'questions' => function ($query) {
            $query->select('id', 'exam_id', 'question_number');
        }])
        ->whereIn('id', $examIds)
        ->get()
        ->keyBy('id');
    }

    /**
     * Optimize database connections for bulk operations
     * Note: Some optimizations require elevated privileges and will be skipped if not available
     * Note: Autocommit is NOT modified here since the import service manages transactions explicitly
     */
    public function optimizeForBulkOperations(): void
    {
        // Disable foreign key checks temporarily for better performance
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } catch (\Exception $e) {
            Log::debug('Could not disable foreign key checks', ['error' => $e->getMessage()]);
        }
        
        // Note: We intentionally do NOT modify autocommit here because the import service
        // uses explicit DB::beginTransaction()/commit()/rollBack() calls.
        // Setting autocommit=0 would create an implicit transaction that conflicts with explicit ones.
    }

    /**
     * Restore normal database settings after bulk operations
     * Note: Some settings require elevated privileges and will be skipped if not available
     */
    public function restoreNormalSettings(): void
    {
        // Re-enable foreign key checks
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            Log::debug('Could not re-enable foreign key checks', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Analyze query performance and suggest optimizations
     */
    public function analyzeQueryPerformance(string $query): array
    {
        try {
            $explainResult = DB::select("EXPLAIN {$query}");
            
            $analysis = [
                'using_index' => false,
                'full_table_scan' => false,
                'estimated_rows' => 0,
                'suggestions' => [],
            ];

            foreach ($explainResult as $row) {
                if (isset($row->key) && $row->key !== null) {
                    $analysis['using_index'] = true;
                }
                
                if (isset($row->type) && $row->type === 'ALL') {
                    $analysis['full_table_scan'] = true;
                    $analysis['suggestions'][] = 'Consider adding an index to avoid full table scan';
                }
                
                if (isset($row->rows)) {
                    $analysis['estimated_rows'] += $row->rows;
                }
            }

            if ($analysis['estimated_rows'] > 10000) {
                $analysis['suggestions'][] = 'Query processes many rows, consider adding WHERE clauses or better indexing';
            }

            return $analysis;

        } catch (\Exception $e) {
            Log::warning('Query analysis failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [
                'using_index' => null,
                'full_table_scan' => null,
                'estimated_rows' => null,
                'suggestions' => ['Query analysis failed'],
            ];
        }
    }
}