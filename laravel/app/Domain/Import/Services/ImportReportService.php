<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Models\ImportSession;
use App\Domain\Import\Models\ImportResult;
use App\Domain\Exam\Models\Exam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ImportReportService
{
    /**
     * Generate comprehensive import statistics for a session.
     */
    public function generateReport(ImportSession $session): array
    {
        $result = $session->result;
        
        if (!$result) {
            return $this->generateEmptyReport($session);
        }

        return [
            'session' => $this->formatSessionData($session),
            'statistics' => $this->generateStatistics($result),
            'success_details' => $this->formatSuccessDetails($result),
            'error_details' => $this->formatErrorDetails($result),
            'affected_exams' => $this->getAffectedExams($result),
            'downloadable_reports' => $this->getDownloadableReports($session, $result),
        ];
    }

    /**
     * Generate detailed error reports with row information.
     */
    public function generateErrorReport(ImportResult $result): array
    {
        if (!$result->errors) {
            return [];
        }

        $errorReport = [];
        foreach ($result->errors as $error) {
            $errorReport[] = [
                'row_number' => $error['row_number'] ?? $error['row'] ?? 'Desconhecido',
                'error_type' => $error['type'] ?? 'Erro Geral',
                'field' => $error['field'] ?? null,
                'message' => $error['message'] ?? 'Erro desconhecido',
                'data' => $error['data'] ?? null,
                'timestamp' => $error['timestamp'] ?? now()->toISOString(),
            ];
        }

        // Sort by row number for easier reading
        usort($errorReport, function ($a, $b) {
            $rowA = is_numeric($a['row_number']) ? (int)$a['row_number'] : PHP_INT_MAX;
            $rowB = is_numeric($b['row_number']) ? (int)$b['row_number'] : PHP_INT_MAX;
            return $rowA <=> $rowB;
        });

        return $errorReport;
    }

    /**
     * Group success details by exam.
     */
    public function groupSuccessDetailsByExam(ImportResult $result): array
    {
        if (!$result->success_details) {
            return [];
        }

        $grouped = [];
        foreach ($result->success_details as $detail) {
            $examId = $detail['exam_id'] ?? 'unknown';
            $examName = $detail['exam_name'] ?? 'Simulado Desconhecido';
            
            if (!isset($grouped[$examId])) {
                // Try to get career name from exam if not in detail
                $careerName = $detail['career_name'] ?? null;
                if (!$careerName && $examId !== 'unknown') {
                    $exam = Exam::with('career')->find($examId);
                    $careerName = $exam?->career?->name ?? 'Carreira Desconhecida';
                }
                
                $grouped[$examId] = [
                    'exam_id' => $examId,
                    'exam_name' => $examName,
                    'career_name' => $careerName ?? 'Carreira Desconhecida',
                    'questions_imported' => 0,
                    'question_details' => [],
                ];
            }
            
            $grouped[$examId]['questions_imported']++;
            
            // Get statement from question if available
            $statementPreview = '';
            if (!empty($detail['question_id'])) {
                $question = \App\Domain\Exam\Models\Question::find($detail['question_id']);
                $statementPreview = $question ? $this->truncateText($question->statement, 100) : '';
            }
            
            $grouped[$examId]['question_details'][] = [
                'question_number' => $detail['question_number'] ?? null,
                'statement_preview' => $statementPreview ?: $this->truncateText($detail['statement'] ?? '', 100),
                'row_number' => $detail['row_number'] ?? $detail['row'] ?? null,
            ];
        }

        // Sort by exam name for consistent display
        uasort($grouped, function ($a, $b) {
            return strcmp($a['exam_name'], $b['exam_name']);
        });

        return $grouped;
    }

    /**
     * Create downloadable error log as text file.
     */
    public function createErrorLogFile(ImportSession $session, ImportResult $result): ?string
    {
        if (!$result->hasFailures()) {
            return null;
        }

        $errorReport = $this->generateErrorReport($result);
        $content = $this->formatErrorLogContent($session, $result, $errorReport);
        
        $filename = "import_errors_{$session->id}_{$session->created_at->format('Y-m-d_H-i-s')}.txt";
        $path = "temp/import_logs/{$filename}";
        
        Storage::put($path, $content);
        
        return $path;
    }

    /**
     * Get links to view affected exams.
     */
    public function getAffectedExamLinks(ImportResult $result): array
    {
        $successDetails = $this->groupSuccessDetailsByExam($result);
        $links = [];
        
        foreach ($successDetails as $examData) {
            if ($examData['exam_id'] !== 'unknown') {
                $exam = Exam::with('career')->find($examData['exam_id']);
                if ($exam) {
                    $links[] = [
                        'exam_id' => $exam->id,
                        'exam_name' => $exam->title,
                        'career_name' => $exam->career->name ?? 'Carreira Desconhecida',
                        'questions_added' => $examData['questions_imported'],
                        'view_url' => route('admin.exams.show', $exam->id),
                        'edit_url' => route('admin.exams.edit', $exam->id),
                    ];
                }
            }
        }
        
        return $links;
    }

    /**
     * Generate statistics summary.
     */
    private function generateStatistics(ImportResult $result): array
    {
        return [
            'total_processed' => $result->total_processed,
            'successful_imports' => $result->successful_imports,
            'failed_imports' => $result->failed_imports,
            'success_rate' => $result->success_rate,
            'failure_rate' => $result->failure_rate,
            'processing_time' => $result->formatted_processing_time,
            'processing_time_seconds' => $result->processing_time,
        ];
    }

    /**
     * Format session data for report.
     */
    private function formatSessionData(ImportSession $session): array
    {
        return [
            'id' => $session->id,
            'filename' => $session->filename,
            'total_rows' => $session->total_rows,
            'status' => $session->status,
            'created_at' => $session->created_at->format('Y-m-d H:i:s'),
            'created_by' => $session->creator->name ?? 'Unknown User',
            'career_mappings_count' => count($session->career_mappings ?? []),
        ];
    }

    /**
     * Format success details for display.
     */
    private function formatSuccessDetails(ImportResult $result): array
    {
        return $this->groupSuccessDetailsByExam($result);
    }

    /**
     * Format error details for display.
     */
    private function formatErrorDetails(ImportResult $result): array
    {
        $errors = $this->generateErrorReport($result);
        
        // Group errors by type for summary
        $errorsByType = [];
        foreach ($errors as $error) {
            $type = $error['error_type'];
            if (!isset($errorsByType[$type])) {
                $errorsByType[$type] = [];
            }
            $errorsByType[$type][] = $error;
        }
        
        return [
            'total_errors' => count($errors),
            'errors_by_type' => $errorsByType,
            'detailed_errors' => $errors,
        ];
    }

    /**
     * Get affected exams information.
     */
    private function getAffectedExams(ImportResult $result): array
    {
        return $this->getAffectedExamLinks($result);
    }

    /**
     * Get downloadable reports information.
     */
    private function getDownloadableReports(ImportSession $session, ImportResult $result): array
    {
        $reports = [];
        
        if ($result->hasFailures()) {
            $errorLogPath = $this->createErrorLogFile($session, $result);
            if ($errorLogPath) {
                $reports['error_log'] = [
                    'name' => 'Error Log',
                    'description' => 'Detailed error report with row numbers and descriptions',
                    'path' => $errorLogPath,
                    'download_url' => route('admin.import.download-error-log', $session->id),
                ];
            }
        }
        
        return $reports;
    }

    /**
     * Generate empty report for sessions without results.
     */
    private function generateEmptyReport(ImportSession $session): array
    {
        return [
            'session' => $this->formatSessionData($session),
            'statistics' => [
                'total_processed' => 0,
                'successful_imports' => 0,
                'failed_imports' => 0,
                'success_rate' => 0,
                'failure_rate' => 0,
                'processing_time' => 'Não disponível',
                'processing_time_seconds' => 0,
            ],
            'success_details' => [],
            'error_details' => [
                'total_errors' => 0,
                'errors_by_type' => [],
                'detailed_errors' => [],
            ],
            'affected_exams' => [],
            'downloadable_reports' => [],
        ];
    }

    /**
     * Format error log content for text file.
     */
    private function formatErrorLogContent(ImportSession $session, ImportResult $result, array $errorReport): string
    {
        $content = [];
        $content[] = "IMPORT ERROR LOG";
        $content[] = "================";
        $content[] = "";
        $content[] = "Session ID: {$session->id}";
        $content[] = "Filename: {$session->filename}";
        $content[] = "Import Date: {$session->created_at->format('Y-m-d H:i:s')}";
        $content[] = "Total Rows Processed: {$result->total_processed}";
        $content[] = "Successful Imports: {$result->successful_imports}";
        $content[] = "Failed Imports: {$result->failed_imports}";
        $content[] = "Success Rate: {$result->success_rate}%";
        $content[] = "";
        $content[] = "DETAILED ERRORS";
        $content[] = "===============";
        $content[] = "";
        
        foreach ($errorReport as $error) {
            $content[] = "Row {$error['row_number']}: {$error['error_type']}";
            if ($error['field']) {
                $content[] = "  Field: {$error['field']}";
            }
            $content[] = "  Message: {$error['message']}";
            if ($error['data']) {
                $content[] = "  Data: " . json_encode($error['data'], JSON_UNESCAPED_UNICODE);
            }
            $content[] = "  Time: {$error['timestamp']}";
            $content[] = "";
        }
        
        return implode("\n", $content);
    }

    /**
     * Truncate text to specified length.
     */
    private function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length - 3) . '...';
    }
}