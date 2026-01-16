<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Support\Facades\Log;

final class DuplicateDetectionService
{
    private const SIMILARITY_THRESHOLD = 0.85;
    private const STATEMENT_MIN_LENGTH = 20;

    /**
     * Check if a question is a duplicate within the given exam
     *
     * @param Exam $exam Target exam to check for duplicates
     * @param array $questionData Validated question data
     * @return array Duplicate detection result with details
     */
    public function checkForDuplicate(Exam $exam, array $questionData): array
    {
        // First check for exact duplicates (fast check)
        $exactDuplicate = $this->findExactDuplicate($exam, $questionData);
        if ($exactDuplicate) {
            return [
                'is_duplicate' => true,
                'type' => 'exact',
                'duplicate_question_id' => $exactDuplicate->id,
                'duplicate_question_number' => $exactDuplicate->question_number,
                'similarity_score' => 1.0,
                'reason' => 'Questão idêntica encontrada (enunciado e resposta correta)',
            ];
        }

        // Check for similar duplicates (more comprehensive but slower)
        $similarDuplicate = $this->findSimilarDuplicate($exam, $questionData);
        if ($similarDuplicate) {
            return [
                'is_duplicate' => true,
                'type' => 'similar',
                'duplicate_question_id' => $similarDuplicate['question']->id,
                'duplicate_question_number' => $similarDuplicate['question']->question_number,
                'similarity_score' => $similarDuplicate['similarity'],
                'reason' => sprintf(
                    'Questão similar encontrada (%.1f%% de similaridade)',
                    $similarDuplicate['similarity'] * 100
                ),
            ];
        }

        return [
            'is_duplicate' => false,
            'type' => null,
            'duplicate_question_id' => null,
            'duplicate_question_number' => null,
            'similarity_score' => 0.0,
            'reason' => null,
        ];
    }

    /**
     * Find exact duplicate question
     *
     * @param Exam $exam Target exam
     * @param array $questionData Question data to check
     * @return Question|null Exact duplicate question or null
     */
    private function findExactDuplicate(Exam $exam, array $questionData): ?Question
    {
        return Question::where('exam_id', $exam->id)
            ->where('statement', $questionData['statement'])
            ->where('correct_answer', $questionData['correct_answer'])
            ->first();
    }

    /**
     * Find similar duplicate question using text similarity
     *
     * @param Exam $exam Target exam
     * @param array $questionData Question data to check
     * @return array|null Similar duplicate with similarity score or null
     */
    private function findSimilarDuplicate(Exam $exam, array $questionData): ?array
    {
        $statement = $questionData['statement'];
        $correctAnswer = $questionData['correct_answer'];

        // Skip similarity check for very short statements
        if (strlen($statement) < self::STATEMENT_MIN_LENGTH) {
            return null;
        }

        // Get all questions from the exam for comparison
        $existingQuestions = Question::where('exam_id', $exam->id)
            ->select('id', 'question_number', 'statement', 'correct_answer')
            ->get();

        $bestMatch = null;
        $highestSimilarity = 0.0;

        foreach ($existingQuestions as $existingQuestion) {
            // Skip if correct answers don't match
            if ($existingQuestion->correct_answer !== $correctAnswer) {
                continue;
            }

            // Calculate text similarity
            $similarity = $this->calculateTextSimilarity($statement, $existingQuestion->statement);

            if ($similarity >= self::SIMILARITY_THRESHOLD && $similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $existingQuestion;
            }
        }

        if ($bestMatch) {
            return [
                'question' => $bestMatch,
                'similarity' => $highestSimilarity,
            ];
        }

        return null;
    }

    /**
     * Calculate text similarity between two strings
     *
     * @param string $text1 First text
     * @param string $text2 Second text
     * @return float Similarity score between 0.0 and 1.0
     */
    private function calculateTextSimilarity(string $text1, string $text2): float
    {
        // Normalize texts for comparison
        $normalized1 = $this->normalizeText($text1);
        $normalized2 = $this->normalizeText($text2);

        // Use Levenshtein distance for similarity calculation
        $maxLength = max(strlen($normalized1), strlen($normalized2));
        
        if ($maxLength === 0) {
            return 1.0; // Both strings are empty
        }

        $distance = levenshtein($normalized1, $normalized2);
        $similarity = 1.0 - ($distance / $maxLength);

        return max(0.0, $similarity);
    }

    /**
     * Normalize text for comparison
     *
     * @param string $text Text to normalize
     * @return string Normalized text
     */
    private function normalizeText(string $text): string
    {
        // Convert to lowercase
        $normalized = strtolower($text);

        // Remove extra whitespace
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Remove common punctuation that doesn't affect meaning
        $normalized = preg_replace('/[.,;:!?()"\'-]/', '', $normalized);

        // Remove accents and special characters
        $normalized = $this->removeAccents($normalized);

        return trim($normalized);
    }

    /**
     * Remove accents from text
     *
     * @param string $text Text with accents
     * @return string Text without accents
     */
    private function removeAccents(string $text): string
    {
        $accents = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N',
        ];

        return strtr($text, $accents);
    }

    /**
     * Log duplicate detection result
     *
     * @param array $duplicateResult Duplicate detection result
     * @param array $questionData Question data that was checked
     * @param int $rowNumber Row number for logging
     */
    public function logDuplicateDetection(array $duplicateResult, array $questionData, int $rowNumber): void
    {
        if ($duplicateResult['is_duplicate']) {
            Log::warning('Duplicate question detected and skipped', [
                'row_number' => $rowNumber,
                'duplicate_type' => $duplicateResult['type'],
                'similarity_score' => $duplicateResult['similarity_score'],
                'duplicate_question_id' => $duplicateResult['duplicate_question_id'],
                'duplicate_question_number' => $duplicateResult['duplicate_question_number'],
                'reason' => $duplicateResult['reason'],
                'statement_preview' => substr($questionData['statement'], 0, 100) . '...',
            ]);
        }
    }

    /**
     * Get duplicate detection statistics for a batch
     *
     * @param array $duplicateResults Array of duplicate detection results
     * @return array Statistics about duplicates found
     */
    public function getDuplicateStatistics(array $duplicateResults): array
    {
        $totalChecked = count($duplicateResults);
        $duplicatesFound = array_filter($duplicateResults, fn($result) => $result['is_duplicate']);
        $exactDuplicates = array_filter($duplicatesFound, fn($result) => $result['type'] === 'exact');
        $similarDuplicates = array_filter($duplicatesFound, fn($result) => $result['type'] === 'similar');

        return [
            'total_checked' => $totalChecked,
            'duplicates_found' => count($duplicatesFound),
            'exact_duplicates' => count($exactDuplicates),
            'similar_duplicates' => count($similarDuplicates),
            'duplicate_rate' => $totalChecked > 0 ? count($duplicatesFound) / $totalChecked : 0.0,
        ];
    }
}