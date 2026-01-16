<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

final class ExamCountMaintenanceService
{
    /**
     * Update question counts for exams after successful imports
     *
     * @param array $successDetails Success details from import containing exam_id information
     * @return array Summary of count updates performed
     */
    public function updateExamQuestionCounts(array $successDetails): array
    {
        if (empty($successDetails)) {
            return [
                'exams_updated' => 0,
                'total_questions_added' => 0,
                'updates' => [],
                'errors' => [],
            ];
        }

        // Group success details by exam_id to count questions per exam
        $examCounts = $this->groupQuestionsByExam($successDetails);
        
        $updatedExams = 0;
        $totalQuestionsAdded = 0;
        $updates = [];
        $errors = [];

        foreach ($examCounts as $examId => $questionsAdded) {
            try {
                $updateResult = $this->updateSingleExamCount($examId, $questionsAdded);
                
                if ($updateResult['success']) {
                    $updatedExams++;
                    $totalQuestionsAdded += $questionsAdded;
                    $updates[] = $updateResult['details'];
                } else {
                    $errors[] = $updateResult['error'];
                }

            } catch (\Exception $e) {
                $error = [
                    'exam_id' => $examId,
                    'questions_added' => $questionsAdded,
                    'error' => $e->getMessage(),
                    'type' => 'exception',
                ];
                
                $errors[] = $error;
                
                Log::error('Failed to update exam question count', $error);
            }
        }

        $summary = [
            'exams_updated' => $updatedExams,
            'total_questions_added' => $totalQuestionsAdded,
            'updates' => $updates,
            'errors' => $errors,
        ];

        Log::info('Exam count maintenance completed', $summary);

        return $summary;
    }

    /**
     * Update question count for a single exam
     *
     * @param int $examId Exam ID to update
     * @param int $questionsAdded Number of questions added
     * @return array Update result with success status and details
     */
    private function updateSingleExamCount(int $examId, int $questionsAdded): array
    {
        $exam = Exam::find($examId);
        
        if (!$exam) {
            return [
                'success' => false,
                'error' => [
                    'exam_id' => $examId,
                    'questions_added' => $questionsAdded,
                    'error' => 'Exam not found',
                    'type' => 'not_found',
                ],
            ];
        }

        // Get current actual count from database
        $actualCount = $this->getActualQuestionCount($examId);
        
        // Verify the count is accurate
        $countVerification = $this->verifyQuestionCount($exam, $actualCount);
        
        if (!$countVerification['accurate']) {
            Log::warning('Question count discrepancy detected', [
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'actual_count' => $actualCount,
                'verification_details' => $countVerification,
            ]);
        }

        // Touch the exam to refresh any cached counts
        $exam->touch();

        return [
            'success' => true,
            'details' => [
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'questions_added' => $questionsAdded,
                'total_questions' => $actualCount,
                'count_accurate' => $countVerification['accurate'],
                'updated_at' => $exam->fresh()->updated_at->toISOString(),
            ],
        ];
    }

    /**
     * Group success details by exam_id and count questions
     *
     * @param array $successDetails Success details from import
     * @return array Array of exam_id => question_count
     */
    private function groupQuestionsByExam(array $successDetails): array
    {
        $examCounts = [];
        
        foreach ($successDetails as $detail) {
            $examId = $detail['exam_id'] ?? null;
            
            if ($examId) {
                $examCounts[$examId] = ($examCounts[$examId] ?? 0) + 1;
            }
        }

        return $examCounts;
    }

    /**
     * Get actual question count from database
     *
     * @param int $examId Exam ID
     * @return int Actual question count
     */
    private function getActualQuestionCount(int $examId): int
    {
        return Question::where('exam_id', $examId)->count();
    }

    /**
     * Verify question count accuracy
     *
     * @param Exam $exam Exam to verify
     * @param int $actualCount Actual count from database
     * @return array Verification result with accuracy status and details
     */
    private function verifyQuestionCount(Exam $exam, int $actualCount): array
    {
        // Check for gaps in question numbering
        $questionNumbers = Question::where('exam_id', $exam->id)
            ->orderBy('question_number')
            ->pluck('question_number')
            ->toArray();

        $gaps = $this->findNumberingGaps($questionNumbers);
        $duplicateNumbers = $this->findDuplicateNumbers($questionNumbers);
        
        // Check if the highest number matches the count
        $maxNumber = !empty($questionNumbers) ? max($questionNumbers) : 0;
        $numberingConsistent = ($maxNumber === $actualCount) && empty($gaps) && empty($duplicateNumbers);

        return [
            'accurate' => $numberingConsistent,
            'actual_count' => $actualCount,
            'max_question_number' => $maxNumber,
            'numbering_gaps' => $gaps,
            'duplicate_numbers' => $duplicateNumbers,
            'numbering_consistent' => $numberingConsistent,
        ];
    }

    /**
     * Find gaps in question numbering sequence
     *
     * @param array $questionNumbers Array of question numbers
     * @return array Array of missing numbers
     */
    private function findNumberingGaps(array $questionNumbers): array
    {
        if (empty($questionNumbers)) {
            return [];
        }

        sort($questionNumbers);
        $gaps = [];
        $expected = 1;

        foreach ($questionNumbers as $number) {
            while ($expected < $number) {
                $gaps[] = $expected;
                $expected++;
            }
            $expected = $number + 1;
        }

        return $gaps;
    }

    /**
     * Find duplicate question numbers
     *
     * @param array $questionNumbers Array of question numbers
     * @return array Array of duplicate numbers
     */
    private function findDuplicateNumbers(array $questionNumbers): array
    {
        $counts = array_count_values($questionNumbers);
        return array_keys(array_filter($counts, fn($count) => $count > 1));
    }

    /**
     * Recalculate and fix question counts for all exams
     * This method can be used for maintenance or recovery
     *
     * @param array $examIds Optional array of specific exam IDs to fix, or null for all exams
     * @return array Summary of fixes applied
     */
    public function recalculateAllExamCounts(array $examIds = null): array
    {
        $query = Exam::query();
        
        if ($examIds !== null) {
            $query->whereIn('id', $examIds);
        }

        $exams = $query->get();
        $fixed = 0;
        $errors = [];
        $details = [];

        foreach ($exams as $exam) {
            try {
                $actualCount = $this->getActualQuestionCount($exam->id);
                $verification = $this->verifyQuestionCount($exam, $actualCount);
                
                // Touch exam to refresh counts
                $exam->touch();
                
                $details[] = [
                    'exam_id' => $exam->id,
                    'exam_title' => $exam->title,
                    'question_count' => $actualCount,
                    'verification' => $verification,
                ];
                
                $fixed++;

            } catch (\Exception $e) {
                $errors[] = [
                    'exam_id' => $exam->id,
                    'exam_title' => $exam->title,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'exams_processed' => count($exams),
            'exams_fixed' => $fixed,
            'errors' => $errors,
            'details' => $details,
        ];
    }

    /**
     * Renumber questions in an exam to fix gaps or duplicates
     *
     * @param int $examId Exam ID to renumber
     * @return array Renumbering result
     */
    public function renumberExamQuestions(int $examId): array
    {
        $exam = Exam::find($examId);
        
        if (!$exam) {
            return [
                'success' => false,
                'error' => 'Exam not found',
            ];
        }

        DB::beginTransaction();

        try {
            // Get all questions ordered by current number, then by ID for consistency
            $questions = Question::where('exam_id', $examId)
                ->orderBy('question_number')
                ->orderBy('id')
                ->get();

            $renumbered = 0;
            $newNumber = 1;

            foreach ($questions as $question) {
                if ($question->question_number !== $newNumber) {
                    $question->update(['question_number' => $newNumber]);
                    $renumbered++;
                }
                $newNumber++;
            }

            DB::commit();

            Log::info('Renumbered exam questions', [
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'total_questions' => $questions->count(),
                'questions_renumbered' => $renumbered,
            ]);

            return [
                'success' => true,
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'total_questions' => $questions->count(),
                'questions_renumbered' => $renumbered,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to renumber exam questions', [
                'exam_id' => $examId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}