<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Edduz\Repositories\EdduzWebhookLogRepository;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WebhookHistoryController extends Controller
{
    /**
     * GET /admin/webhooks/edduz — Lista paginada com filtros.
     */
    public function index(Request $request, EdduzWebhookLogRepository $repo): View
    {
        $this->authorize('admin');

        $filters = $request->only(['status', 'event_type', 'date_from', 'date_to']);

        $logs = $repo->paginate($filters);

        return view('admin.webhooks.edduz.index', compact('logs', 'filters'));
    }

    /**
     * GET /admin/webhooks/edduz/{id} — Detalhes completos do registro.
     */
    public function show(int $id, EdduzWebhookLogRepository $repo): View
    {
        $this->authorize('admin');

        $log = $repo->findOrFail($id);

        return view('admin.webhooks.edduz.show', compact('log'));
    }
}
