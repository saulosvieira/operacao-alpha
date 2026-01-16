<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Career\Repositories\CareerRepository;
use App\Domain\Career\DTOs\CareerData;
use App\Domain\Exam\Models\Exam;

final class CareerMappingValidationService
{
    public function __construct(
        private readonly CareerRepository $careerRepository
    ) {
    }

    /**
     * Validate that mapped careers exist and are active
     *
     * @param array $mappings Array with abbreviation as key and career_id as value
     * @return array Validation result with detailed error information
     */
    public function validateCareerMappings(array $mappings): array
    {
        $errors = [];
        $validMappings = [];
        $warnings = [];

        foreach ($mappings as $abbreviation => $careerId) {
            $validationResult = $this->validateSingleMapping($abbreviation, $careerId);
            
            if (!$validationResult['valid']) {
                $errors[$abbreviation] = $validationResult['error'];
            } else {
                $validMappings[$abbreviation] = $validationResult['career'];
                
                if (!empty($validationResult['warnings'])) {
                    $warnings[$abbreviation] = $validationResult['warnings'];
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'mappings' => $validMappings
        ];
    }

    /**
     * Validate a single career mapping
     *
     * @param string $abbreviation Career abbreviation
     * @param mixed $careerId Career ID to validate
     * @return array Validation result for single mapping
     */
    private function validateSingleMapping(string $abbreviation, mixed $careerId): array
    {
        // Check if career ID is provided
        if (empty($careerId)) {
            return [
                'valid' => false,
                'error' => "Career mapping is required for abbreviation: {$abbreviation}",
                'career' => null,
                'warnings' => []
            ];
        }

        // Validate career ID is numeric
        if (!is_numeric($careerId)) {
            return [
                'valid' => false,
                'error' => "Invalid career ID format for abbreviation {$abbreviation}: {$careerId}",
                'career' => null,
                'warnings' => []
            ];
        }

        $careerId = (int) $careerId;

        // Check if career exists and is active
        $career = $this->careerRepository->findActiveById($careerId);
        
        if (!$career) {
            return [
                'valid' => false,
                'error' => "Career with ID {$careerId} not found or inactive for abbreviation: {$abbreviation}",
                'career' => null,
                'warnings' => []
            ];
        }

        // Check if career has active exams
        $examValidation = $this->validateCareerHasActiveExams($career);
        
        if (!$examValidation['valid']) {
            return [
                'valid' => false,
                'error' => $examValidation['error'],
                'career' => null,
                'warnings' => []
            ];
        }

        return [
            'valid' => true,
            'error' => null,
            'career' => $career,
            'warnings' => $examValidation['warnings'] ?? []
        ];
    }

    /**
     * Validate that career has at least one active exam
     *
     * @param CareerData $career Career to validate
     * @return array Validation result with warnings
     */
    private function validateCareerHasActiveExams(CareerData $career): array
    {
        $warnings = [];

        // Check if career has any exams at all
        if ($career->examsCount === 0) {
            return [
                'valid' => false,
                'error' => "Career '{$career->name}' has no exams available for question assignment",
                'warnings' => []
            ];
        }

        // Additional validation: check if exams are actually active
        $activeExamsCount = $this->getActiveExamsCount($career->id);
        
        if ($activeExamsCount === 0) {
            return [
                'valid' => false,
                'error' => "Career '{$career->name}' has no active exams available for question assignment",
                'warnings' => []
            ];
        }

        // Add warning if career has few active exams
        if ($activeExamsCount === 1) {
            $warnings[] = "Career '{$career->name}' has only one active exam available";
        }

        return [
            'valid' => true,
            'error' => null,
            'warnings' => $warnings
        ];
    }

    /**
     * Get count of active exams for a career
     *
     * @param int $careerId Career ID
     * @return int Number of active exams
     */
    private function getActiveExamsCount(int $careerId): int
    {
        return Exam::where('career_id', $careerId)
            ->where('active', true)
            ->count();
    }

    /**
     * Validate that all required abbreviations are mapped
     *
     * @param array $abbreviations Required abbreviations from Excel
     * @param array $mappings Provided mappings
     * @return array Validation result for completeness
     */
    public function validateMappingCompleteness(array $abbreviations, array $mappings): array
    {
        $missingMappings = [];
        
        foreach ($abbreviations as $abbreviation) {
            if (!isset($mappings[$abbreviation]) || empty($mappings[$abbreviation])) {
                $missingMappings[] = $abbreviation;
            }
        }

        return [
            'complete' => empty($missingMappings),
            'missing' => $missingMappings,
            'message' => empty($missingMappings) 
                ? 'All career abbreviations are mapped' 
                : 'Missing mappings for: ' . implode(', ', $missingMappings)
        ];
    }

    /**
     * Validate mapping consistency across multiple imports
     *
     * @param array $newMappings New mappings to validate
     * @param array $existingMappings Previously used mappings (optional)
     * @return array Validation result for consistency
     */
    public function validateMappingConsistency(array $newMappings, array $existingMappings = []): array
    {
        if (empty($existingMappings)) {
            return [
                'consistent' => true,
                'conflicts' => [],
                'message' => 'No previous mappings to compare against'
            ];
        }

        $conflicts = [];
        
        foreach ($newMappings as $abbreviation => $careerId) {
            if (isset($existingMappings[$abbreviation]) && 
                $existingMappings[$abbreviation] !== $careerId) {
                
                $conflicts[$abbreviation] = [
                    'previous' => $existingMappings[$abbreviation],
                    'current' => $careerId
                ];
            }
        }

        return [
            'consistent' => empty($conflicts),
            'conflicts' => $conflicts,
            'message' => empty($conflicts) 
                ? 'All mappings are consistent with previous imports' 
                : 'Found mapping conflicts for: ' . implode(', ', array_keys($conflicts))
        ];
    }
}