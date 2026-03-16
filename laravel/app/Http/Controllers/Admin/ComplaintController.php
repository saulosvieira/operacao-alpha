<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Complaint\Actions\CreateComplaintAction;
use App\Domain\Complaint\Actions\ListComplaintsAction;
use App\Domain\Complaint\Actions\UpdateComplaintStatusAction;
use App\Domain\Complaint\Enums\ComplaintPriority;
use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Enums\ComplaintType;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    /**
     * GET /admin/complaints — Lista paginada de reclamações com filtros.
     */
    public function index(Request $request, ListComplaintsAction $action): View
    {
        $this->authorize('admin');

        $filters = $request->only(['status', 'type', 'priority']);

        $complaints = $action->execute($filters);

        return view('admin.complaints.index', [
            'complaints' => $complaints,
            'filters' => $filters,
            'types' => ComplaintType::cases(),
            'priorities' => ComplaintPriority::cases(),
            'statuses' => ComplaintStatus::cases(),
        ]);
    }

    /**
     * POST /admin/complaints — Registra nova reclamação sobre uma questão.
     */
    public function store(Request $request, CreateComplaintAction $action): RedirectResponse
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'type' => ['required', Rule::in(array_column(ComplaintType::cases(), 'value'))],
            'description' => ['required', 'string'],
            'priority' => ['required', Rule::in(array_column(ComplaintPriority::cases(), 'value'))],
        ]);

        $action->execute(
            questionId: (int) $validated['question_id'],
            adminId: $request->user()->id,
            type: ComplaintType::from($validated['type']),
            description: $validated['description'],
            priority: ComplaintPriority::from($validated['priority']),
        );

        return redirect()->back()->with('success', 'Reclamação registrada com sucesso.');
    }

    /**
     * PATCH /admin/complaints/{complaint}/status — Atualiza status de uma reclamação.
     */
    public function updateStatus(
        Request $request,
        int $complaintId,
        UpdateComplaintStatusAction $action,
    ): RedirectResponse {
        $this->authorize('admin');

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_column(ComplaintStatus::cases(), 'value'))],
            'resolution_note' => ['nullable', 'string'],
        ]);

        $action->execute(
            complaintId: $complaintId,
            status: ComplaintStatus::from($validated['status']),
            resolutionNote: $validated['resolution_note'] ?? null,
        );

        return redirect()->back()->with('success', 'Status da reclamação atualizado com sucesso.');
    }
}
