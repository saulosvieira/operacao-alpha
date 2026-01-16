<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Career\Repositories\CareerRepository;
use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use Illuminate\Support\Collection;

final class CareerMappingService
{
    public function __construct(
        private readonly CareerRepository $careerRepository,
        private readonly CareerMappingValidationService $validationService
    ) {
    }

    /**
     * Extract unique career abbreviations from Excel data
     *
     * @param Collection $data Collection of rows from Excel file
     * @return array Array of unique career abbreviations
     */
    public function extractAbbreviations(Collection $data): array
    {
        return $data
            ->pluck('career_abbreviation')
            ->filter()
            ->map(fn($abbr) => trim(strtoupper($abbr)))
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Suggest career mappings based on fuzzy matching
     *
     * @param array $abbreviations Array of career abbreviations
     * @return array Array with abbreviation as key and suggested career data as value
     */
    public function suggestMappings(array $abbreviations): array
    {
        $careers = $this->careerRepository->getAllActive();
        $suggestions = [];

        foreach ($abbreviations as $abbreviation) {
            $suggestion = $this->findBestMatch($abbreviation, $careers);
            $suggestions[$abbreviation] = $suggestion;
        }

        return $suggestions;
    }

    /**
     * Validate career mappings against database
     *
     * @param array $mappings Array with abbreviation as key and career_id as value
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public function validateMappings(array $mappings): array
    {
        return $this->validationService->validateCareerMappings($mappings);
    }

    /**
     * Apply career mappings to Excel data
     *
     * @param Collection $data Original Excel data
     * @param array $mappings Validated career mappings
     * @return Collection Data with career_id added to each row
     */
    public function applyMappings(Collection $data, array $mappings): Collection
    {
        // Load career objects for the mapped IDs
        $careerIds = array_values($mappings);
        $careers = Career::whereIn('id', $careerIds)->get()->keyBy('id');
        
        return $data->map(function ($row) use ($mappings, $careers) {
            $abbreviation = trim(strtoupper($row['career_abbreviation'] ?? ''));
            
            if (isset($mappings[$abbreviation])) {
                $careerId = $mappings[$abbreviation];
                $career = $careers->get($careerId);
                
                if ($career) {
                    $row['career_id'] = $career->id;
                    $row['career_name'] = $career->name;
                    $row['career_mapped'] = true;
                } else {
                    $row['career_mapped'] = false;
                }
            } else {
                $row['career_mapped'] = false;
            }
            
            return $row;
        });
    }

    /**
     * Find best matching career for an abbreviation using fuzzy matching
     *
     * @param string $abbreviation Career abbreviation to match
     * @param Collection $careers Available careers
     * @return CareerData|null Best matching career or null if no good match
     */
    private function findBestMatch(string $abbreviation, Collection $careers): ?CareerData
    {
        $abbreviation = strtoupper(trim($abbreviation));
        
        // First try exact matches with common patterns
        $exactMatches = $this->getExactMatches($abbreviation);
        foreach ($careers as $career) {
            $careerName = strtoupper($career->name);
            if (in_array($careerName, $exactMatches) || str_contains($careerName, $abbreviation)) {
                return $career;
            }
        }

        // Then try fuzzy matching based on similarity
        $bestMatch = null;
        $bestScore = 0;

        foreach ($careers as $career) {
            $score = $this->calculateSimilarity($abbreviation, $career);
            if ($score > $bestScore && $score > 0.6) { // Minimum 60% similarity
                $bestScore = $score;
                $bestMatch = $career;
            }
        }

        return $bestMatch;
    }

    /**
     * Get exact matches for common career abbreviations
     *
     * @param string $abbreviation Career abbreviation
     * @return array Array of possible full career names
     */
    private function getExactMatches(string $abbreviation): array
    {
        $commonMappings = [
            'PM' => ['POLÍCIA MILITAR', 'POLICIA MILITAR'],
            'PC' => ['POLÍCIA CIVIL', 'POLICIA CIVIL'],
            'PRF' => ['POLÍCIA RODOVIÁRIA FEDERAL', 'POLICIA RODOVIARIA FEDERAL'],
            'PF' => ['POLÍCIA FEDERAL', 'POLICIA FEDERAL'],
            'CBM' => ['CORPO DE BOMBEIROS MILITAR', 'BOMBEIROS'],
            'TJ' => ['TRIBUNAL DE JUSTIÇA', 'TRIBUNAL DE JUSTICA'],
            'TRF' => ['TRIBUNAL REGIONAL FEDERAL'],
            'TRT' => ['TRIBUNAL REGIONAL DO TRABALHO'],
            'TSE' => ['TRIBUNAL SUPERIOR ELEITORAL'],
            'STF' => ['SUPREMO TRIBUNAL FEDERAL'],
            'STJ' => ['SUPERIOR TRIBUNAL DE JUSTIÇA', 'SUPERIOR TRIBUNAL DE JUSTICA'],
            'TCU' => ['TRIBUNAL DE CONTAS DA UNIÃO', 'TRIBUNAL DE CONTAS DA UNIAO'],
            'INSS' => ['INSTITUTO NACIONAL DO SEGURO SOCIAL'],
            'IBAMA' => ['INSTITUTO BRASILEIRO DO MEIO AMBIENTE'],
            'ANAC' => ['AGÊNCIA NACIONAL DE AVIAÇÃO CIVIL', 'AGENCIA NACIONAL DE AVIACAO CIVIL'],
            'ANVISA' => ['AGÊNCIA NACIONAL DE VIGILÂNCIA SANITÁRIA', 'AGENCIA NACIONAL DE VIGILANCIA SANITARIA'],
            'ANP' => ['AGÊNCIA NACIONAL DO PETRÓLEO', 'AGENCIA NACIONAL DO PETROLEO'],
            'ANTT' => ['AGÊNCIA NACIONAL DE TRANSPORTES TERRESTRES', 'AGENCIA NACIONAL DE TRANSPORTES TERRESTRES'],
        ];

        return $commonMappings[$abbreviation] ?? [$abbreviation];
    }

    /**
     * Calculate similarity score between abbreviation and career
     *
     * @param string $abbreviation Career abbreviation
     * @param CareerData $career Career data
     * @return float Similarity score between 0 and 1
     */
    private function calculateSimilarity(string $abbreviation, CareerData $career): float
    {
        $careerName = strtoupper($career->name);
        
        // Check if abbreviation matches career initials
        $initials = $this->extractInitials($careerName);
        if ($initials === $abbreviation) {
            return 1.0;
        }

        // Check if abbreviation is contained in career name
        if (str_contains($careerName, $abbreviation)) {
            return 0.8;
        }

        // Use Levenshtein distance for fuzzy matching
        $distance = levenshtein($abbreviation, $careerName);
        $maxLength = max(strlen($abbreviation), strlen($careerName));
        
        return $maxLength > 0 ? 1 - ($distance / $maxLength) : 0;
    }

    /**
     * Extract initials from career name
     *
     * @param string $careerName Career name
     * @return string Career initials
     */
    private function extractInitials(string $careerName): string
    {
        $words = explode(' ', $careerName);
        $initials = '';
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, ['DE', 'DA', 'DO', 'DOS', 'DAS', 'E'])) {
                $initials .= substr($word, 0, 1);
            }
        }
        
        return $initials;
    }

    /**
     * Check if career has at least one active exam
     *
     * @param CareerData $career Career to check
     * @return bool True if career has active exams
     */
    private function hasActiveExams(CareerData $career): bool
    {
        // Use the examsCount from the DTO, or query directly if needed
        return $career->examsCount > 0;
    }

    /**
     * Validate mapping completeness
     *
     * @param array $abbreviations Required abbreviations
     * @param array $mappings Provided mappings
     * @return array Validation result
     */
    public function validateMappingCompleteness(array $abbreviations, array $mappings): array
    {
        return $this->validationService->validateMappingCompleteness($abbreviations, $mappings);
    }

    /**
     * Validate mapping consistency
     *
     * @param array $newMappings New mappings
     * @param array $existingMappings Existing mappings
     * @return array Validation result
     */
    public function validateMappingConsistency(array $newMappings, array $existingMappings = []): array
    {
        return $this->validationService->validateMappingConsistency($newMappings, $existingMappings);
    }
}