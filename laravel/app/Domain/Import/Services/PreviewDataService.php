<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use Illuminate\Support\Collection;

final class PreviewDataService
{
    public function __construct(
        private readonly QuestionValidationService $questionValidationService
    ) {
    }

    /**
     * Generate preview data for import
     *
     * @param Collection $questionsData Collection of question data
     * @param array $careerMappings Career mappings (abbreviation => CareerData)
     * @return array Preview data with statistics and sample questions
     */
    public function generatePreview(Collection $questionsData, array $careerMappings): array
    {
        // Validate all questions
        $validationResults = $this->questionValidationService->validateQuestions($questionsData);
        
        // Data already has career mappings applied, just ensure row numbers
        $mappedData = $questionsData->map(function ($question, $index) {
            $question['row_number'] = $index + 2; // Assuming row 1 is header
            return $question;
        });
        
        // Generate statistics
        $statistics = $this->generateStatistics($mappedData, $validationResults);
        
        // Get sample questions for preview
        $sampleQuestions = $this->getSampleQuestions($mappedData, $validationResults['validation_results']);
        
        // Group questions by career
        $questionsByCareer = $this->groupQuestionsByCareer($mappedData, $careerMappings);
        
        // Get validation errors with highlighting
        $validationErrors = $this->getValidationErrorsWithHighlighting($validationResults);
        
        return [
            'statistics' => $statistics,
            'sample_questions' => $sampleQuestions,
            'questions_by_career' => $questionsByCareer,
            'validation_errors' => $validationErrors,
            'validation_summary' => $validationResults['summary'],
            'total_questions' => $questionsData->count(),
            'valid_questions' => $validationResults['valid_questions'],
            'invalid_questions' => $validationResults['invalid_questions'],
            'career_mappings' => $careerMappings,
        ];
    }

    /**
     * Apply career mappings to question data
     *
     * @param Collection $questionsData Original question data
     * @param array $careerMappings Career mappings
     * @return Collection Mapped question data
     */
    private function applyCareerMappings(Collection $questionsData, array $careerMappings): Collection
    {
        return $questionsData->map(function ($question, $index) use ($careerMappings) {
            $abbreviation = strtoupper(trim($question['career_abbreviation'] ?? ''));
            
            $question['row_number'] = $index + 2; // Assuming row 1 is header
            
            if (isset($careerMappings[$abbreviation])) {
                $career = $careerMappings[$abbreviation];
                $question['career_id'] = $career->id;
                $question['career_name'] = $career->name;
                $question['career_mapped'] = true;
            } else {
                $question['career_id'] = null;
                $question['career_name'] = null;
                $question['career_mapped'] = false;
            }
            
            return $question;
        });
    }

    /**
     * Generate statistics for preview
     *
     * @param Collection $mappedData Mapped question data
     * @param array $validationResults Validation results
     * @return array Statistics
     */
    private function generateStatistics(Collection $mappedData, array $validationResults): array
    {
        $totalQuestions = $mappedData->count();
        $validQuestions = $validationResults['valid_questions'];
        $invalidQuestions = $validationResults['invalid_questions'];
        
        // Count questions by career
        $questionsByCareer = $mappedData->groupBy('career_name')->map->count();
        
        // Count unmapped careers
        $unmappedQuestions = $mappedData->where('career_mapped', false)->count();
        
        // Calculate success rate
        $successRate = $totalQuestions > 0 ? round(($validQuestions / $totalQuestions) * 100, 2) : 0;
        
        return [
            'total_questions' => $totalQuestions,
            'valid_questions' => $validQuestions,
            'invalid_questions' => $invalidQuestions,
            'unmapped_questions' => $unmappedQuestions,
            'success_rate' => $successRate,
            'questions_by_career' => $questionsByCareer->toArray(),
            'unique_careers' => $questionsByCareer->count(),
            'ready_for_import' => $validQuestions > 0 && $unmappedQuestions === 0,
        ];
    }

    /**
     * Get sample questions for preview (first 10 valid questions)
     *
     * @param Collection $mappedData Mapped question data
     * @param array $validationResults Individual validation results
     * @return array Sample questions with validation status
     */
    private function getSampleQuestions(Collection $mappedData, array $validationResults): array
    {
        $sampleQuestions = [];
        $count = 0;
        $maxSamples = 10;
        
        foreach ($mappedData as $index => $question) {
            if ($count >= $maxSamples) {
                break;
            }
            
            $validationResult = $validationResults[$index] ?? null;
            
            $sampleQuestion = [
                'row_number' => $question['row_number'],
                'statement' => $this->truncateText($question['statement'] ?? '', 200),
                'option_a' => $this->truncateText($question['option_a'] ?? '', 100),
                'option_b' => $this->truncateText($question['option_b'] ?? '', 100),
                'option_c' => $this->truncateText($question['option_c'] ?? '', 100),
                'option_d' => $this->truncateText($question['option_d'] ?? '', 100),
                'option_e' => $this->truncateText($question['option_e'] ?? '', 100),
                'correct_answer' => $question['correct_answer'] ?? '',
                'career_abbreviation' => $question['career_abbreviation'] ?? '',
                'career_name' => $question['career_name'] ?? 'Não mapeada',
                'career_mapped' => $question['career_mapped'] ?? false,
                'is_valid' => $validationResult['valid'] ?? false,
                'validation_errors' => $validationResult['errors'] ?? [],
                'has_errors' => !empty($validationResult['errors'] ?? []),
                'explanation' => $question['explanation'] ?? '',
            ];
            
            $sampleQuestions[] = $sampleQuestion;
            $count++;
        }
        
        return $sampleQuestions;
    }

    /**
     * Group questions by career for statistics
     *
     * @param Collection $mappedData Mapped question data
     * @param array $careerMappings Career mappings
     * @return array Questions grouped by career
     */
    private function groupQuestionsByCareer(Collection $mappedData, array $careerMappings): array
    {
        $groupedQuestions = [];
        
        foreach ($careerMappings as $abbreviation => $careerId) {
            $upperAbbreviation = strtoupper(trim($abbreviation));
            
            $careerQuestions = $mappedData->filter(function ($q) use ($upperAbbreviation) {
                $qAbbrev = strtoupper(trim($q['career_abbreviation'] ?? ''));
                return $qAbbrev === $upperAbbreviation;
            });
            
            if ($careerQuestions->isNotEmpty()) {
                // Get career name from the first question (already mapped)
                $firstQuestion = $careerQuestions->first();
                
                $validCount = $careerQuestions->filter(function ($question) {
                    return $this->questionValidationService->hasRequiredFields($question);
                })->count();
                
                $totalCount = $careerQuestions->count();
                
                // Ensure career_id is an integer
                $careerIdInt = is_numeric($careerId) ? (int) $careerId : null;
                
                $groupedQuestions[] = [
                    'career_id' => $careerIdInt,
                    'career_name' => $firstQuestion['career_name'] ?? 'Unknown',
                    'career_abbreviation' => $abbreviation,
                    'question_count' => $totalCount,
                    'valid_questions' => $validCount,
                    'valid_count' => $validCount,
                    'invalid_count' => $totalCount - $validCount,
                    'total_count' => $totalCount,
                    'sample_questions' => $careerQuestions->take(3)->map(function ($question) {
                        return [
                            'row_number' => $question['row_number'],
                            'statement' => $this->truncateText($question['statement'] ?? '', 150),
                            'correct_answer' => $question['correct_answer'] ?? '',
                        ];
                    })->toArray(),
                ];
            }
        }
        
        // Add unmapped questions
        $unmappedQuestions = $mappedData->where('career_mapped', false);
        if ($unmappedQuestions->isNotEmpty()) {
            $groupedQuestions[] = [
                'career_id' => null,
                'career_name' => 'Carreiras não mapeadas',
                'career_abbreviation' => null,
                'question_count' => $unmappedQuestions->count(),
                'valid_questions' => 0,
                'sample_questions' => $unmappedQuestions->take(3)->map(function ($question) {
                    return [
                        'row_number' => $question['row_number'],
                        'statement' => $this->truncateText($question['statement'] ?? '', 150),
                        'career_abbreviation' => $question['career_abbreviation'] ?? '',
                    ];
                })->toArray(),
            ];
        }
        
        return $groupedQuestions;
    }

    /**
     * Get validation errors with highlighting information
     *
     * @param array $validationResults Validation results
     * @return array Formatted validation errors
     */
    private function getValidationErrorsWithHighlighting(array $validationResults): array
    {
        $errorsByRow = [];
        $errorsByType = [];
        
        foreach ($validationResults['validation_results'] as $result) {
            if (!$result['valid'] && !empty($result['errors'])) {
                $rowNumber = $result['row_number'];
                
                $errorsByRow[$rowNumber] = [
                    'row_number' => $rowNumber,
                    'errors' => $result['errors'],
                    'error_count' => count($result['errors']),
                    'data' => $result['data'] ?? [],
                ];
                
                // Categorize errors by type for highlighting
                foreach ($result['errors'] as $error) {
                    $errorType = $this->categorizeError($error);
                    $errorsByType[$errorType][] = [
                        'row_number' => $rowNumber,
                        'error' => $error,
                    ];
                }
            }
        }
        
        return [
            'errors_by_row' => $errorsByRow,
            'errors_by_type' => $errorsByType,
            'total_error_rows' => count($errorsByRow),
            'total_errors' => count($validationResults['errors']),
            'error_summary' => $this->generateErrorSummary($errorsByType),
        ];
    }

    /**
     * Categorize error by type for highlighting
     *
     * @param string $error Error message
     * @return string Error category
     */
    private function categorizeError(string $error): string
    {
        if (str_contains($error, 'enunciado')) {
            return 'statement';
        } elseif (str_contains($error, 'alternativa')) {
            return 'options';
        } elseif (str_contains($error, 'resposta correta')) {
            return 'correct_answer';
        } elseif (str_contains($error, 'carreira')) {
            return 'career';
        } elseif (str_contains($error, 'explicação')) {
            return 'explanation';
        } else {
            return 'other';
        }
    }

    /**
     * Generate error summary for display
     *
     * @param array $errorsByType Errors grouped by type
     * @return array Error summary
     */
    private function generateErrorSummary(array $errorsByType): array
    {
        $summary = [];
        
        $errorTypeLabels = [
            'statement' => 'Problemas no enunciado',
            'options' => 'Problemas nas alternativas',
            'correct_answer' => 'Problemas na resposta correta',
            'career' => 'Problemas na carreira',
            'explanation' => 'Problemas na explicação',
            'other' => 'Outros problemas',
        ];
        
        foreach ($errorsByType as $type => $errors) {
            $summary[] = [
                'type' => $type,
                'label' => $errorTypeLabels[$type] ?? 'Outros problemas',
                'count' => count($errors),
                'affected_rows' => array_unique(array_column($errors, 'row_number')),
            ];
        }
        
        // Sort by count descending
        usort($summary, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        return $summary;
    }

    /**
     * Truncate text for display
     *
     * @param string $text Text to truncate
     * @param int $maxLength Maximum length
     * @return string Truncated text
     */
    private function truncateText(string $text, int $maxLength): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        return substr($text, 0, $maxLength - 3) . '...';
    }

    /**
     * Generate preview for specific career
     *
     * @param Collection $questionsData Question data
     * @param string $careerAbbreviation Career abbreviation
     * @param array $careerMappings Career mappings
     * @return array Career-specific preview
     */
    public function generateCareerPreview(Collection $questionsData, string $careerAbbreviation, array $careerMappings): array
    {
        $careerQuestions = $questionsData->filter(function ($question) use ($careerAbbreviation) {
            return strtoupper(trim($question['career_abbreviation'] ?? '')) === strtoupper($careerAbbreviation);
        });
        
        if ($careerQuestions->isEmpty()) {
            return [
                'career_abbreviation' => $careerAbbreviation,
                'career_name' => $careerMappings[$careerAbbreviation]->name ?? 'Não encontrada',
                'question_count' => 0,
                'questions' => [],
                'validation_errors' => [],
            ];
        }
        
        $validationResults = $this->questionValidationService->validateQuestions($careerQuestions);
        
        return [
            'career_abbreviation' => $careerAbbreviation,
            'career_name' => $careerMappings[$careerAbbreviation]->name ?? 'Não mapeada',
            'question_count' => $careerQuestions->count(),
            'valid_questions' => $validationResults['valid_questions'],
            'invalid_questions' => $validationResults['invalid_questions'],
            'questions' => $careerQuestions->take(20)->map(function ($question, $index) use ($validationResults) {
                $validationResult = $validationResults['validation_results'][$index] ?? null;
                
                return [
                    'row_number' => $index + 2,
                    'statement' => $question['statement'] ?? '',
                    'options' => [
                        'A' => $question['option_a'] ?? '',
                        'B' => $question['option_b'] ?? '',
                        'C' => $question['option_c'] ?? '',
                        'D' => $question['option_d'] ?? '',
                        'E' => $question['option_e'] ?? '',
                    ],
                    'correct_answer' => $question['correct_answer'] ?? '',
                    'explanation' => $question['explanation'] ?? '',
                    'is_valid' => $validationResult['valid'] ?? false,
                    'validation_errors' => $validationResult['errors'] ?? [],
                ];
            })->toArray(),
            'validation_errors' => $validationResults['errors'],
            'validation_summary' => $validationResults['summary'],
        ];
    }

    /**
     * Check if preview data is ready for import
     *
     * @param array $previewData Preview data
     * @return array Readiness check result
     */
    public function checkImportReadiness(array $previewData): array
    {
        $issues = [];
        $warnings = [];
        
        // Check if there are valid questions
        if ($previewData['valid_questions'] === 0) {
            $issues[] = 'Nenhuma questão válida encontrada para importação.';
        }
        
        // Check if all careers are mapped
        if ($previewData['statistics']['unmapped_questions'] > 0) {
            $issues[] = sprintf(
                '%d questões com carreiras não mapeadas.',
                $previewData['statistics']['unmapped_questions']
            );
        }
        
        // Check success rate
        if ($previewData['statistics']['success_rate'] < 50) {
            $warnings[] = sprintf(
                'Taxa de sucesso baixa: %.2f%%. Considere revisar os dados.',
                $previewData['statistics']['success_rate']
            );
        }
        
        // Check for common validation errors
        if (!empty($previewData['validation_errors']['error_summary'])) {
            foreach ($previewData['validation_errors']['error_summary'] as $errorSummary) {
                if ($errorSummary['count'] > ($previewData['total_questions'] * 0.1)) {
                    $warnings[] = sprintf(
                        '%s em %d questões (%.1f%% do total).',
                        $errorSummary['label'],
                        $errorSummary['count'],
                        ($errorSummary['count'] / $previewData['total_questions']) * 100
                    );
                }
            }
        }
        
        return [
            'ready' => empty($issues),
            'can_proceed' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings,
            'recommendation' => $this->generateImportRecommendation($previewData, $issues, $warnings),
        ];
    }

    /**
     * Generate import recommendation based on preview data
     *
     * @param array $previewData Preview data
     * @param array $issues Critical issues
     * @param array $warnings Warnings
     * @return string Recommendation message
     */
    private function generateImportRecommendation(array $previewData, array $issues, array $warnings): string
    {
        if (!empty($issues)) {
            return 'Não é possível prosseguir com a importação. Corrija os problemas identificados e tente novamente.';
        }
        
        if (empty($warnings)) {
            return 'Dados prontos para importação. Todas as validações foram aprovadas.';
        }
        
        if ($previewData['statistics']['success_rate'] >= 80) {
            return 'Dados em boa qualidade. Você pode prosseguir com a importação, mas considere revisar os avisos.';
        }
        
        if ($previewData['statistics']['success_rate'] >= 60) {
            return 'Dados com qualidade moderada. Recomenda-se revisar e corrigir os problemas antes da importação.';
        }
        
        return 'Dados com muitos problemas. Recomenda-se fortemente revisar e corrigir antes de prosseguir.';
    }
}