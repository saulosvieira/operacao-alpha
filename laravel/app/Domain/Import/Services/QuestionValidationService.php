<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class QuestionValidationService
{
    /**
     * Validation rules for question data
     * Note: option_e is optional (questions can have 4 or 5 alternatives)
     */
    private const VALIDATION_RULES = [
        'statement' => 'required|string|min:10|max:5000',
        'option_a' => 'required|string|max:1000',
        'option_b' => 'required|string|max:1000',
        'option_c' => 'required|string|max:1000',
        'option_d' => 'required|string|max:1000',
        'option_e' => 'nullable|string|max:1000',
        'correct_answer' => 'required|in:A,B,C,D,E',
        'explanation' => 'nullable|string',
        'career_abbreviation' => 'required|string',
    ];

    /**
     * Custom validation messages
     */
    private const VALIDATION_MESSAGES = [
        'statement.required' => 'O enunciado da questão é obrigatório.',
        'statement.min' => 'O enunciado deve ter pelo menos 10 caracteres.',
        'statement.max' => 'O enunciado não pode exceder 5000 caracteres.',
        'option_a.required' => 'A alternativa A é obrigatória.',
        'option_a.max' => 'A alternativa A não pode exceder 1000 caracteres.',
        'option_b.required' => 'A alternativa B é obrigatória.',
        'option_b.max' => 'A alternativa B não pode exceder 1000 caracteres.',
        'option_c.required' => 'A alternativa C é obrigatória.',
        'option_c.max' => 'A alternativa C não pode exceder 1000 caracteres.',
        'option_d.required' => 'A alternativa D é obrigatória.',
        'option_d.max' => 'A alternativa D não pode exceder 1000 caracteres.',
        'option_e.max' => 'A alternativa E não pode exceder 1000 caracteres.',
        'correct_answer.required' => 'A resposta correta é obrigatória.',
        'correct_answer.in' => 'A resposta correta deve ser A, B, C, D ou E.',
        'career_abbreviation.required' => 'A sigla da carreira é obrigatória.',
    ];

    /**
     * Validate a single question row
     *
     * @param array $questionData Question data to validate
     * @param int $rowNumber Row number for error reporting
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public function validateQuestion(array $questionData, int $rowNumber): array
    {
        try {
            // Clean and prepare data
            $cleanData = $this->prepareQuestionData($questionData);
            
            // Create validator
            $validator = Validator::make($cleanData, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
            
            // Add custom validation rules
            $this->addCustomValidationRules($validator, $cleanData, $rowNumber);
            
            if ($validator->fails()) {
                return [
                    'valid' => false,
                    'errors' => $this->formatValidationErrors($validator->errors()->toArray(), $rowNumber),
                    'row_number' => $rowNumber,
                    'data' => $cleanData,
                ];
            }
            
            return [
                'valid' => true,
                'errors' => [],
                'row_number' => $rowNumber,
                'data' => $cleanData,
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'errors' => ["Linha {$rowNumber}: Erro interno na validação - {$e->getMessage()}"],
                'row_number' => $rowNumber,
                'data' => $questionData,
            ];
        }
    }

    /**
     * Validate multiple questions in batch
     *
     * @param Collection $questionsData Collection of question data
     * @return array Validation results with statistics
     */
    public function validateQuestions(Collection $questionsData): array
    {
        $results = [
            'total_questions' => $questionsData->count(),
            'valid_questions' => 0,
            'invalid_questions' => 0,
            'validation_results' => [],
            'errors' => [],
            'summary' => [],
        ];

        foreach ($questionsData as $index => $questionData) {
            $rowNumber = $index + 2; // Assuming row 1 is header
            $validation = $this->validateQuestion($questionData, $rowNumber);
            
            $results['validation_results'][] = $validation;
            
            if ($validation['valid']) {
                $results['valid_questions']++;
            } else {
                $results['invalid_questions']++;
                $results['errors'] = array_merge($results['errors'], $validation['errors']);
            }
        }

        $results['summary'] = $this->generateValidationSummary($results);
        
        return $results;
    }

    /**
     * Prepare and clean question data
     *
     * @param array $questionData Raw question data
     * @return array Cleaned question data
     */
    private function prepareQuestionData(array $questionData): array
    {
        return [
            'statement' => $this->cleanText($questionData['statement'] ?? ''),
            'option_a' => $this->cleanText($questionData['option_a'] ?? ''),
            'option_b' => $this->cleanText($questionData['option_b'] ?? ''),
            'option_c' => $this->cleanText($questionData['option_c'] ?? ''),
            'option_d' => $this->cleanText($questionData['option_d'] ?? ''),
            'option_e' => $this->cleanText($questionData['option_e'] ?? ''),
            'correct_answer' => strtoupper(trim($questionData['correct_answer'] ?? '')),
            'explanation' => $this->cleanText($questionData['explanation'] ?? ''),
            'career_abbreviation' => strtoupper(trim($questionData['career_abbreviation'] ?? '')),
        ];
    }

    /**
     * Clean text content
     *
     * @param string $text Text to clean
     * @return string Cleaned text
     */
    private function cleanText(string $text): string
    {
        // Remove extra whitespace and normalize line breaks
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        // Remove null bytes and other control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        return $text;
    }

    /**
     * Add custom validation rules
     *
     * @param \Illuminate\Validation\Validator $validator Validator instance
     * @param array $data Data being validated
     * @param int $rowNumber Row number for error reporting
     */
    private function addCustomValidationRules($validator, array $data, int $rowNumber): void
    {
        // Validate that options are not duplicated
        $validator->after(function ($validator) use ($data, $rowNumber) {
            $options = [
                'A' => $data['option_a'] ?? '',
                'B' => $data['option_b'] ?? '',
                'C' => $data['option_c'] ?? '',
                'D' => $data['option_d'] ?? '',
                'E' => $data['option_e'] ?? '',
            ];
            
            $this->validateUniqueOptions($validator, $options, $rowNumber);
            $this->validateStatementContent($validator, $data['statement'] ?? '', $rowNumber);
            $this->validateOptionsContent($validator, $options, $rowNumber);
        });
    }

    /**
     * Validate that options are unique
     *
     * @param \Illuminate\Validation\Validator $validator Validator instance
     * @param array $options Question options
     * @param int $rowNumber Row number for error reporting
     */
    private function validateUniqueOptions($validator, array $options, int $rowNumber): void
    {
        $cleanOptions = array_map('trim', array_map('strtolower', $options));
        $duplicates = array_diff_assoc($cleanOptions, array_unique($cleanOptions));
        
        if (!empty($duplicates)) {
            $duplicateKeys = array_keys($duplicates);
            $validator->errors()->add(
                'options',
                "Linha {$rowNumber}: As alternativas " . implode(', ', $duplicateKeys) . " são idênticas."
            );
        }
    }

    /**
     * Validate statement content quality
     *
     * @param \Illuminate\Validation\Validator $validator Validator instance
     * @param string $statement Question statement
     * @param int $rowNumber Row number for error reporting
     */
    private function validateStatementContent($validator, string $statement, int $rowNumber): void
    {
        // Check for common issues in statement
        if (empty(trim($statement))) {
            return; // Already handled by required rule
        }

        // Check if statement is too short to be meaningful
        $wordCount = str_word_count($statement);
        if ($wordCount < 3) {
            $validator->errors()->add(
                'statement',
                "Linha {$rowNumber}: O enunciado deve conter pelo menos 3 palavras."
            );
        }
    }

    /**
     * Validate options content quality
     *
     * @param \Illuminate\Validation\Validator $validator Validator instance
     * @param array $options Question options
     * @param int $rowNumber Row number for error reporting
     */
    private function validateOptionsContent($validator, array $options, int $rowNumber): void
    {
        foreach ($options as $key => $option) {
            if (empty(trim($option))) {
                continue; // Already handled by required rule
            }

            // Check for very short options (less than 1 character)
            if (strlen(trim($option)) < 1) {
                $validator->errors()->add(
                    "option_" . strtolower($key),
                    "Linha {$rowNumber}: A alternativa {$key} está vazia."
                );
            }
        }
    }

    /**
     * Format validation errors for display
     *
     * @param array $errors Validation errors
     * @param int $rowNumber Row number
     * @return array Formatted errors
     */
    private function formatValidationErrors(array $errors, int $rowNumber): array
    {
        $formattedErrors = [];
        
        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                // Add row number if not already present
                if (!str_contains($message, "Linha {$rowNumber}")) {
                    $message = "Linha {$rowNumber}: {$message}";
                }
                $formattedErrors[] = $message;
            }
        }
        
        return $formattedErrors;
    }

    /**
     * Generate validation summary
     *
     * @param array $results Validation results
     * @return array Summary statistics
     */
    private function generateValidationSummary(array $results): array
    {
        $errorsByType = [];
        
        foreach ($results['errors'] as $error) {
            if (str_contains($error, 'enunciado')) {
                $errorsByType['statement'] = ($errorsByType['statement'] ?? 0) + 1;
            } elseif (str_contains($error, 'alternativa')) {
                $errorsByType['options'] = ($errorsByType['options'] ?? 0) + 1;
            } elseif (str_contains($error, 'resposta correta')) {
                $errorsByType['correct_answer'] = ($errorsByType['correct_answer'] ?? 0) + 1;
            } elseif (str_contains($error, 'carreira')) {
                $errorsByType['career'] = ($errorsByType['career'] ?? 0) + 1;
            } else {
                $errorsByType['other'] = ($errorsByType['other'] ?? 0) + 1;
            }
        }

        return [
            'total_questions' => $results['total_questions'],
            'valid_questions' => $results['valid_questions'],
            'invalid_questions' => $results['invalid_questions'],
            'success_rate' => $results['total_questions'] > 0 
                ? round(($results['valid_questions'] / $results['total_questions']) * 100, 2) 
                : 0,
            'errors_by_type' => $errorsByType,
            'total_errors' => count($results['errors']),
        ];
    }

    /**
     * Get validation rules
     *
     * @return array Validation rules
     */
    public function getValidationRules(): array
    {
        return self::VALIDATION_RULES;
    }

    /**
     * Get validation messages
     *
     * @return array Validation messages
     */
    public function getValidationMessages(): array
    {
        return self::VALIDATION_MESSAGES;
    }

    /**
     * Check if question data has all required fields
     *
     * @param array $questionData Question data to check
     * @return bool True if all required fields are present
     */
    public function hasRequiredFields(array $questionData): bool
    {
        $requiredFields = ['statement', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer', 'career_abbreviation'];
        
        foreach ($requiredFields as $field) {
            if (empty(trim($questionData[$field] ?? ''))) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get missing required fields
     *
     * @param array $questionData Question data to check
     * @return array Array of missing field names
     */
    public function getMissingRequiredFields(array $questionData): array
    {
        $requiredFields = ['statement', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer', 'career_abbreviation'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty(trim($questionData[$field] ?? ''))) {
                $missingFields[] = $field;
            }
        }
        
        return $missingFields;
    }
}