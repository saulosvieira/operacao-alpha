<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Import\Models\ImportSession;
use App\Domain\Import\Services\ImportReportService;
use App\Domain\Import\Services\QuestionImportService;
use App\Domain\Import\Services\ImportProgressTracker;
use App\Domain\Career\Models\Career;
use App\Domain\Exam\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionImportController extends Controller
{
    public function __construct(
        private ImportReportService $reportService,
        private QuestionImportService $importService,
        private ImportProgressTracker $progressTracker
    ) {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display the import form.
     */
    public function index()
    {
        return view('admin.questions.import.index');
    }

    /**
     * Handle file upload and create import session.
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('excel_file');
            $session = $this->importService->processFile($file, Auth::id());

            return redirect()->route('admin.import.sessions.mapping', $session->id)
                ->with('success', 'File uploaded successfully. Please map the career abbreviations.');

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'user_id' => Auth::id(),
                'filename' => $request->file('excel_file')?->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['excel_file' => $e->getMessage()]);
        }
    }

    /**
     * Show career mapping interface.
     */
    public function showMapping(ImportSession $session)
    {
        try {
            $abbreviations = $this->importService->extractCareerAbbreviations($session);
            $careers = Career::where('active', true)->orderBy('name')->get();

            return view('admin.questions.import.mapping', [
                'session' => $session,
                'abbreviations' => $abbreviations,
                'careers' => $careers,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to extract career abbreviations', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.import.questions.index')
                ->withErrors(['error' => 'Failed to process import file: ' . $e->getMessage()]);
        }
    }

    /**
     * Process career mapping submission.
     */
    public function processMapping(Request $request, ImportSession $session)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*' => 'required|exists:careers,id',
        ]);

        try {
            $mappings = $request->input('mappings');
            $validation = $this->importService->validateMappings($session, $mappings);

            if (!$validation['valid']) {
                return back()->withErrors(['mappings' => $validation['errors']]);
            }

            return redirect()->route('admin.import.sessions.preview', $session->id)
                ->with('success', 'Career mappings saved successfully. Please review the preview.');

        } catch (\Exception $e) {
            Log::error('Career mapping validation failed', [
                'session_id' => $session->id,
                'mappings' => $request->input('mappings'),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to validate career mappings: ' . $e->getMessage()]);
        }
    }

    /**
     * Show import preview.
     */
    public function showPreview(ImportSession $session)
    {
        try {
            $preview = $this->importService->generatePreview($session);

            return view('admin.questions.import.preview', [
                'session' => $session,
                'preview' => $preview,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate preview', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.import.questions.index')
                ->withErrors(['error' => 'Failed to generate preview: ' . $e->getMessage()]);
        }
    }

    /**
     * Execute the import process.
     */
    public function executeImport(Request $request, ImportSession $session)
    {
        $examMappings = [];
        $examOptions = $request->input('exam_option', []);
        $newExamData = $request->input('new_exam', []);
        $existingExamMappings = $request->input('exam_mappings', []);

        try {
            // Process each career's exam destination
            foreach ($examOptions as $careerId => $option) {
                if ($option === 'new' && isset($newExamData[$careerId])) {
                    // Create new exam
                    $examData = $newExamData[$careerId];
                    
                    // Validate new exam data
                    if (empty($examData['title'])) {
                        return back()->withErrors(['error' => 'O nome do simulado é obrigatório para criar um novo simulado.']);
                    }
                    
                    $newExam = Exam::create([
                        'career_id' => $careerId,
                        'title' => $examData['title'],
                        'description' => $examData['description'] ?? null,
                        'time_limit_minutes' => $examData['time_limit'] ?? 180,
                        'feedback_mode' => $examData['feedback_mode'] ?? 'after_submit',
                        'is_free' => isset($examData['is_free']),
                        'active' => true,
                    ]);
                    
                    $examMappings[$careerId] = $newExam->id;
                    
                    Log::info('Created new exam for import', [
                        'exam_id' => $newExam->id,
                        'career_id' => $careerId,
                        'title' => $newExam->title,
                    ]);
                } elseif ($option === 'existing' && !empty($existingExamMappings[$careerId])) {
                    // Use existing exam
                    $examMappings[$careerId] = $existingExamMappings[$careerId];
                }
                // If no option selected or empty, the import service will use default exam
            }

            $result = $this->importService->executeImport($session, $examMappings);

            return redirect()->route('admin.import.sessions.report', $session->id)
                ->with('success', 'Importação concluída com sucesso. Por favor, revise os resultados.');

        } catch (\Exception $e) {
            Log::error('Import execution failed', [
                'session_id' => $session->id,
                'exam_mappings' => $examMappings,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Falha na importação: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel import session.
     */
    public function cancelSession(ImportSession $session)
    {
        try {
            $success = $this->importService->cancelSession($session);

            if ($success) {
                return redirect()->route('admin.import.questions.index')
                    ->with('success', 'Import session cancelled successfully.');
            } else {
                return back()->withErrors(['error' => 'Failed to cancel import session.']);
            }

        } catch (\Exception $e) {
            Log::error('Failed to cancel import session', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to cancel import session: ' . $e->getMessage()]);
        }
    }

    /**
     * Get exams for a specific career (AJAX endpoint).
     */
    public function getCareerExams(Request $request)
    {
        $careerId = $request->input('career_id');
        
        // Handle empty or invalid career_id
        if (empty($careerId) || $careerId === '0' || $careerId === 0) {
            return response()->json([
                'success' => true,
                'exams' => [],
            ]);
        }

        // Validate career exists
        $career = Career::find($careerId);
        if (!$career) {
            return response()->json([
                'success' => false,
                'message' => 'Carreira não encontrada',
                'exams' => [],
            ]);
        }

        $exams = Exam::where('career_id', $careerId)
            ->where('active', true)
            ->withCount('questions')
            ->orderBy('title')
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'question_count' => $exam->questions_count,
                ];
            });

        return response()->json([
            'success' => true,
            'exams' => $exams,
        ]);
    }

    /**
     * Show the import report for a session.
     */
    public function showReport(ImportSession $session)
    {
        $report = $this->reportService->generateReport($session);
        
        return view('admin.questions.import.report', [
            'session' => $session,
            'report' => $report,
        ]);
    }

    /**
     * Download error log for a specific import session.
     */
    public function downloadErrorLog(ImportSession $session): StreamedResponse
    {
        $result = $session->result;
        
        if (!$result || !$result->hasFailures()) {
            abort(404, 'No error log available for this import session.');
        }

        $errorLogPath = $this->reportService->createErrorLogFile($session, $result);
        
        if (!$errorLogPath || !Storage::exists($errorLogPath)) {
            abort(404, 'Error log file not found.');
        }

        $filename = "import_errors_{$session->id}_{$session->created_at->format('Y-m-d_H-i-s')}.txt";

        return Storage::download($errorLogPath, $filename, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Get affected exams data as JSON for AJAX requests.
     */
    public function getAffectedExams(ImportSession $session)
    {
        $result = $session->result;
        
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'No import result found for this session.',
            ], 404);
        }

        $affectedExams = $this->reportService->getAffectedExamLinks($result);
        
        return response()->json([
            'success' => true,
            'data' => $affectedExams,
        ]);
    }

    /**
     * Export import report as JSON.
     */
    public function exportReport(ImportSession $session)
    {
        $report = $this->reportService->generateReport($session);
        
        $filename = "import_report_{$session->id}_{$session->created_at->format('Y-m-d_H-i-s')}.json";
        
        return response()->json($report, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Get import progress for a session (AJAX endpoint).
     */
    public function getImportProgress(ImportSession $session)
    {
        $progress = $this->progressTracker->getProgress($session->id);
        
        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress data not found for this session.',
            ], 404);
        }

        // Calculate additional metrics
        $progressPercentage = $progress['total_rows'] > 0 
            ? round(($progress['processed_rows'] / $progress['total_rows']) * 100, 2)
            : 0;

        $elapsedTime = microtime(true) - $progress['start_time'];
        $estimatedTimeRemaining = null;
        
        if ($progressPercentage > 0 && $progress['status'] === 'processing') {
            $estimatedTotalTime = $elapsedTime / ($progressPercentage / 100);
            $estimatedTimeRemaining = max(0, $estimatedTotalTime - $elapsedTime);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $session->id,
                'status' => $progress['status'],
                'progress_percentage' => $progressPercentage,
                'processed_rows' => $progress['processed_rows'],
                'successful_rows' => $progress['successful_rows'],
                'failed_rows' => $progress['failed_rows'],
                'total_rows' => $progress['total_rows'],
                'current_batch' => $progress['current_batch'],
                'total_batches' => $progress['total_batches'],
                'elapsed_time' => round($elapsedTime, 2),
                'estimated_time_remaining' => $estimatedTimeRemaining ? round($estimatedTimeRemaining, 2) : null,
                'memory_usage' => $this->formatBytes($progress['memory_usage']),
                'peak_memory' => $this->formatBytes($progress['peak_memory']),
                'last_update' => date('Y-m-d H:i:s', $progress['last_update']),
            ],
        ]);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get import statistics for dashboard widgets.
     */
    public function getImportStatistics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        
        $sessions = ImportSession::with('result')
            ->where('created_at', '>=', now()->subDays($period))
            ->where('status', ImportSession::STATUS_COMPLETED)
            ->get();

        $statistics = [
            'total_imports' => $sessions->count(),
            'total_questions_processed' => $sessions->sum(fn($s) => $s->result?->total_processed ?? 0),
            'total_successful_questions' => $sessions->sum(fn($s) => $s->result?->successful_imports ?? 0),
            'total_failed_questions' => $sessions->sum(fn($s) => $s->result?->failed_imports ?? 0),
            'average_success_rate' => $sessions->avg(fn($s) => $s->result?->success_rate ?? 0),
            'recent_imports' => $sessions->take(5)->map(function ($session) {
                return [
                    'id' => $session->id,
                    'filename' => $session->filename,
                    'created_at' => $session->created_at->format('Y-m-d H:i:s'),
                    'success_rate' => $session->result?->success_rate ?? 0,
                    'total_processed' => $session->result?->total_processed ?? 0,
                ];
            }),
        ];

        return response()->json($statistics);
    }
}