<?php

namespace App\Domain\Import\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ExcelQuestionImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * The processed question data.
     */
    private Collection $processedData;

    /**
     * The validation failures.
     */
    private Collection $validationFailures;

    /**
     * Column mapping from Excel headers to internal field names.
     */
    private array $columnMapping = [
        'carreira' => 'career_abbreviation',
        'materia' => 'subject',
        'assunto' => 'topic',
        'enunciado' => 'statement',
        'alternativa_a' => 'option_a',
        'alternativa_b' => 'option_b',
        'alternativa_c' => 'option_c',
        'alternativa_d' => 'option_d',
        'alternativa_e' => 'option_e',
        'correta' => 'correct_answer',
        'comentario' => 'explanation',
        'nivel_dificuldade' => 'difficulty_level',
        'texto_apoio' => 'support_text',
        'link_pdf_apoio' => 'support_pdf_url',
        'ano' => 'year',
        'banca' => 'exam_board',
    ];

    public function __construct()
    {
        $this->processedData = collect();
        $this->validationFailures = collect();
    }

    /**
     * Process the Excel collection.
     */
    public function collection(Collection $rows): void
    {
        // Debug: Log the first row to see actual headers
        if ($rows->isNotEmpty()) {
            $firstRow = $rows->first();
            \Log::info('Excel headers received', [
                'headers' => array_keys($firstRow->toArray()),
                'first_row_data' => $firstRow->toArray()
            ]);
        }

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            
            // Stop processing when we find an empty first column (carreira)
            // This indicates the end of the data
            $firstColumnValue = $rowArray['carreira'] ?? $rowArray[array_key_first($rowArray)] ?? null;
            if ($this->isEmptyValue($firstColumnValue)) {
                \Log::info('Preview generated', [
                    'session_id' => 'N/A',
                    'total_questions' => $this->processedData->count(),
                    'valid_questions' => $this->processedData->filter(fn($row) => $this->isRowComplete($row))->count(),
                    'invalid_questions' => $this->processedData->reject(fn($row) => $this->isRowComplete($row))->count(),
                ]);
                break;
            }
            
            $processedRow = $this->prepareRowData($rowArray, $index + 2); // +2 because of header row and 0-based index
            $this->processedData->push($processedRow);
        }
    }

    /**
     * Check if a value is considered empty.
     */
    private function isEmptyValue($value): bool
    {
        if ($value === null) {
            return true;
        }
        
        if (is_string($value) && trim($value) === '') {
            return true;
        }
        
        return false;
    }

    /**
     * Get validation rules for each row.
     */
    public function getValidationRules(): array
    {
        return [
            'carreira' => 'required|string|max:50',
            'materia' => 'required|string|max:100',
            'assunto' => 'nullable|string|max:200',
            'enunciado' => 'required|string|min:10|max:2000',
            'alternativa_a' => 'required|string|max:500',
            'alternativa_b' => 'required|string|max:500',
            'alternativa_c' => 'required|string|max:500',
            'alternativa_d' => 'required|string|max:500',
            'alternativa_e' => 'required|string|max:500',
            'correta' => 'required|in:A,B,C,D,E',
            'comentario' => 'nullable|string|max:1000',
            'nivel_dificuldade' => 'nullable|in:Fácil,Médio,Difícil,1,2,3',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages(): array
    {
        return [
            'carreira.required' => 'A coluna carreira é obrigatória.',
            'carreira.string' => 'A carreira deve ser um texto.',
            'carreira.max' => 'A carreira não pode ter mais de 50 caracteres.',
            
            'materia.required' => 'A coluna matéria é obrigatória.',
            'materia.string' => 'A matéria deve ser um texto.',
            'materia.max' => 'A matéria não pode ter mais de 100 caracteres.',
            
            'assunto.string' => 'O assunto deve ser um texto.',
            'assunto.max' => 'O assunto não pode ter mais de 200 caracteres.',
            
            'enunciado.required' => 'O enunciado da questão é obrigatório.',
            'enunciado.string' => 'O enunciado deve ser um texto.',
            'enunciado.min' => 'O enunciado deve ter pelo menos 10 caracteres.',
            'enunciado.max' => 'O enunciado não pode ter mais de 2000 caracteres.',
            
            'alternativa_a.required' => 'A alternativa A é obrigatória.',
            'alternativa_a.string' => 'A alternativa A deve ser um texto.',
            'alternativa_a.max' => 'A alternativa A não pode ter mais de 500 caracteres.',
            
            'alternativa_b.required' => 'A alternativa B é obrigatória.',
            'alternativa_b.string' => 'A alternativa B deve ser um texto.',
            'alternativa_b.max' => 'A alternativa B não pode ter mais de 500 caracteres.',
            
            'alternativa_c.required' => 'A alternativa C é obrigatória.',
            'alternativa_c.string' => 'A alternativa C deve ser um texto.',
            'alternativa_c.max' => 'A alternativa C não pode ter mais de 500 caracteres.',
            
            'alternativa_d.required' => 'A alternativa D é obrigatória.',
            'alternativa_d.string' => 'A alternativa D deve ser um texto.',
            'alternativa_d.max' => 'A alternativa D não pode ter mais de 500 caracteres.',
            
            'alternativa_e.required' => 'A alternativa E é obrigatória.',
            'alternativa_e.string' => 'A alternativa E deve ser um texto.',
            'alternativa_e.max' => 'A alternativa E não pode ter mais de 500 caracteres.',
            
            'correta.required' => 'A resposta correta é obrigatória.',
            'correta.in' => 'A resposta correta deve ser A, B, C, D ou E.',
            
            'comentario.string' => 'O comentário deve ser um texto.',
            'comentario.max' => 'O comentário não pode ter mais de 1000 caracteres.',
            
            'nivel_dificuldade.in' => 'O nível de dificuldade deve ser Fácil, Médio, Difícil ou 1, 2, 3.',
        ];
    }

    /**
     * Prepare row data for processing.
     */
    private function prepareRowData(array $row, int $rowNumber): array
    {
        $preparedData = [
            'row_number' => $rowNumber,
            'original_data' => $row,
        ];

        // Map columns to internal field names
        foreach ($this->columnMapping as $excelColumn => $internalField) {
            // Try exact match first
            $value = $row[$excelColumn] ?? null;
            
            // If not found, try variations (with/without accents, underscores)
            if ($value === null) {
                // Try variations of the column name
                $variations = $this->getColumnVariations($excelColumn);
                foreach ($variations as $variation) {
                    if (isset($row[$variation])) {
                        $value = $row[$variation];
                        break;
                    }
                }
            }
            
            // Clean and prepare the value
            $preparedData[$internalField] = $this->cleanValue($value);
        }

        // Normalize correct answer to uppercase
        if (isset($preparedData['correct_answer'])) {
            $preparedData['correct_answer'] = strtoupper(trim($preparedData['correct_answer']));
        }

        // Perform manual validation
        $validationErrors = $this->validateRowData($preparedData, $rowNumber);
        if (!empty($validationErrors)) {
            $this->validationFailures->push([
                'row' => $rowNumber,
                'errors' => $validationErrors,
                'values' => $preparedData,
            ]);
        }

        return $preparedData;
    }

    /**
     * Get variations of a column name to handle different formats.
     */
    private function getColumnVariations(string $columnName): array
    {
        $variations = [$columnName];
        
        // Add version with accents removed
        $withoutAccents = $this->removeAccents($columnName);
        if ($withoutAccents !== $columnName) {
            $variations[] = $withoutAccents;
        }
        
        // Add version with underscores replaced by spaces
        $withSpaces = str_replace('_', ' ', $columnName);
        if ($withSpaces !== $columnName) {
            $variations[] = $withSpaces;
        }
        
        // Add version with spaces replaced by underscores
        $withUnderscores = str_replace(' ', '_', $columnName);
        if ($withUnderscores !== $columnName) {
            $variations[] = $withUnderscores;
        }
        
        return $variations;
    }

    /**
     * Remove accents from a string.
     */
    private function removeAccents(string $string): string
    {
        $unwanted = [
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
        
        return strtr($string, $unwanted);
    }

    /**
     * Validate row data manually.
     */
    private function validateRowData(array $data, int $rowNumber): array
    {
        $errors = [];
        $rules = $this->getValidationRules();
        $messages = $this->customValidationMessages();

        // Map internal fields back to Excel column names for validation
        $excelData = [];
        foreach ($this->columnMapping as $excelColumn => $internalField) {
            $excelData[$excelColumn] = $data[$internalField] ?? null;
        }

        // Validate each field
        foreach ($rules as $field => $rule) {
            $value = $excelData[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[] = $messages[$field . '.required'] ?? "Campo {$field} é obrigatório.";
                continue;
            }
            
            if (!empty($value)) {
                // Check string validation
                if (strpos($rule, 'string') !== false && !is_string($value)) {
                    $errors[] = $messages[$field . '.string'] ?? "Campo {$field} deve ser texto.";
                }
                
                // Check max length
                if (preg_match('/max:(\d+)/', $rule, $matches)) {
                    $maxLength = (int) $matches[1];
                    if (strlen($value) > $maxLength) {
                        $errors[] = $messages[$field . '.max'] ?? "Campo {$field} não pode ter mais de {$maxLength} caracteres.";
                    }
                }
                
                // Check min length
                if (preg_match('/min:(\d+)/', $rule, $matches)) {
                    $minLength = (int) $matches[1];
                    if (strlen($value) < $minLength) {
                        $errors[] = $messages[$field . '.min'] ?? "Campo {$field} deve ter pelo menos {$minLength} caracteres.";
                    }
                }
                
                // Check 'in' validation
                if (preg_match('/in:([^|]+)/', $rule, $matches)) {
                    $allowedValues = explode(',', $matches[1]);
                    if (!in_array($value, $allowedValues)) {
                        $errors[] = $messages[$field . '.in'] ?? "Campo {$field} deve ser um dos valores: " . implode(', ', $allowedValues);
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Clean and prepare a value from Excel.
     */
    private function cleanValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Convert to string and trim whitespace
        $cleaned = trim((string) $value);
        
        // Remove extra whitespace and normalize line breaks
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = str_replace(["\r\n", "\r"], "\n", $cleaned);
        
        return $cleaned === '' ? null : $cleaned;
    }

    /**
     * Handle validation failures.
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->validationFailures->push([
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }

    /**
     * Get the processed data.
     */
    public function getProcessedData(): Collection
    {
        return $this->processedData;
    }

    /**
     * Get validation failures.
     */
    public function getValidationFailures(): Collection
    {
        return $this->validationFailures;
    }

    /**
     * Get unique career abbreviations from the processed data.
     */
    public function getUniqueCareerAbbreviations(): Collection
    {
        return $this->processedData
            ->pluck('career_abbreviation')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * Check if there are any validation failures.
     */
    public function hasValidationFailures(): bool
    {
        return $this->validationFailures->isNotEmpty();
    }

    /**
     * Get the total number of rows processed.
     */
    public function getTotalRows(): int
    {
        return $this->processedData->count();
    }

    /**
     * Get questions grouped by career abbreviation.
     */
    public function getQuestionsByCareer(): Collection
    {
        return $this->processedData->groupBy('career_abbreviation');
    }

    /**
     * Get a preview of the first N questions.
     */
    public function getPreview(int $limit = 10): Collection
    {
        return $this->processedData->take($limit);
    }

    /**
     * Check if a row has all required fields.
     */
    public function isRowComplete(array $row): bool
    {
        $requiredFields = [
            'career_abbreviation', 
            'subject', 
            'statement', 
            'option_a', 
            'option_b', 
            'option_c', 
            'option_d', 
            'option_e', 
            'correct_answer'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get statistics about the import data.
     */
    public function getStatistics(): array
    {
        $totalRows = $this->getTotalRows();
        $validRows = $this->processedData->filter(fn($row) => $this->isRowComplete($row))->count();
        $invalidRows = $totalRows - $validRows;
        $uniqueCareers = $this->getUniqueCareerAbbreviations()->count();

        return [
            'total_rows' => $totalRows,
            'valid_rows' => $validRows,
            'invalid_rows' => $invalidRows,
            'unique_careers' => $uniqueCareers,
            'validation_failures' => $this->validationFailures->count(),
        ];
    }
}