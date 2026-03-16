<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Career\Models\Career;
use App\Domain\Exam\Actions\Admin\ExportAttemptsCsvAction;
use App\Domain\Exam\Actions\Admin\GetAttemptDetailAction;
use App\Domain\Exam\Actions\Admin\GetExamStatisticsAction;
use App\Domain\Exam\Actions\Admin\ListAttemptsForAdminAction;
use App\Domain\Exam\Models\Exam;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttemptController extends Controller
{
    /**
     * GET /admin/attempts — Lista paginada de tentativas com filtros e estatísticas.
     */
    public function index(
        Request $request,
        ListAttemptsForAdminAction $listAction,
        GetExamStatisticsAction $statsAction,
    ): View {
        $this->authorize('admin');

        $filters = $request->only(['search', 'exam_id', 'career_id', 'date_from', 'date_to']);

        $attempts = $listAction->execute($filters);
        $statistics = $statsAction->execute($filters);
        $exams = Exam::orderBy('title')->get();
        $careers = Career::orderBy('name')->get();

        return view('admin.attempts.index', compact(
            'attempts',
            'statistics',
            'filters',
            'exams',
            'careers',
        ));
    }

    /**
     * GET /admin/attempts/{attempt} — Detalhe de uma tentativa.
     */
    public function show(int $attemptId, GetAttemptDetailAction $action): View
    {
        $this->authorize('admin');

        $detail = $action->execute($attemptId);

        return view('admin.attempts.show', [
            'attempt' => $detail['attempt'],
            'answers' => $detail['answers'],
            'questionStats' => $detail['questionStats'],
            'complaints' => $detail['complaints'],
        ]);
    }

    /**
     * GET /admin/attempts/export — Exportação CSV das tentativas filtradas.
     */
    public function export(Request $request, ExportAttemptsCsvAction $action): StreamedResponse
    {
        $this->authorize('admin');

        $filters = $request->only(['search', 'exam_id', 'career_id', 'date_from', 'date_to']);

        return $action->execute($filters);
    }

    /**
     * GET /admin/attempts/export/count — Retorna contagem para confirmação antes de exportar.
     */
    public function exportCount(Request $request, ListAttemptsForAdminAction $action): JsonResponse
    {
        $this->authorize('admin');

        $filters = $request->only(['search', 'exam_id', 'career_id', 'date_from', 'date_to']);

        return response()->json([
            'count' => $action->count($filters),
        ]);
    }
}
